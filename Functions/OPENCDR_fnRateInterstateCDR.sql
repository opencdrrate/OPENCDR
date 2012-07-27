CREATE OR REPLACE FUNCTION "fnRateInterstateCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 500000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 10
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 10;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective Term Rate Sheet
TRUNCATE TABLE effectiveinterstateratemaster;

INSERT INTO effectiveinterstateratemaster 
SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM interstateratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM interstateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTER_EFFECTIVE_RATE_DATE', CDRDate);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generated % new rates. Completed in: %', rec_count, age(EndDateTime, gentime);

INSERT INTO processhistory VALUES('Interstate Rates Generated', gentime, EndDateTime, age(EndDateTime, gentime), rec_count);

END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrinter (
	CallID varchar(100)     PRIMARY KEY,
	CustomerID varchar(15),
	CallType smallint,
	CallDateTime timestamp NOT NULL,
	Duration integer NOT NULL,
	BilledDuration integer,
	Direction char(1),
	SourceIP varchar(15),
	OriginatingNumber varchar(50) NOT NULL,
	DestinationNumber varchar(50) NOT NULL,
	BilledPrefix varchar(10),
	LRN      varchar(50),
	LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
	BilledNumber varchar(50), 
	NPANXXX char(7),
	NPANXX char(6),
	RetailRate numeric(9,7),
	RetailPrice numeric(19,7),
	CNAMDipped boolean,
	CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
        RoutingPrefix varchar(10),
	RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrinter (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, lrn, lrndipfee, billednumber, npanxxx, npanxx, retailrate, retailprice, cnamdipped, cnamfee, wholesalerate, wholesaleprice, RoutingPrefix)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, LRN, 0, null, null, null, null, null, CNAMdipped, 0, wholesalerate, wholesaleprice, RoutingPrefix
FROM callRecordMaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 10
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrinter.sourceip = ipaddressmaster.ipaddress and cdrinter.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid CustomerID.'
FROM cdrinter where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter where coalesce(customerid, '') = '');

DELETE FROM cdrinter where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrinter_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrinter_rawduration ON cdrinter (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrinter WHERE duration <= 0;
DELETE FROM callrecordmaster_tbr WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrinter SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--massage source/dest into E.164 if possible
update cdrinter set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update cdrinter set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update cdrinter set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) <> '+';
update cdrinter set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update cdrinter set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';
update cdrinter set LRN = '+1' || lrn where char_length(LRN) = 10 and substring(LRN from 1 for 1) <> '+';
update cdrinter set LRN = '+' || lrn where char_length(LRN) = 11 and substring(LRN from 1 for 1) = '1';
update cdrinter set LRN = '+' || substring(LRN from 4 for 20) where substring(LRN from 1 for 3) = '011';



-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrinter SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrinter SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- filter out BilledNumbers with invalid format 
RAISE NOTICE 'filter out BilledNumbers with invalid fomat to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid BilledNumber Format. Must be E.164.'
FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1';

DELETE FROM callrecordmaster_tbr WHERE callid IN (SELECT CallID FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1');

DELETE FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'Creating index CustomerID_NPANXXX.'; gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrinter(CustomerID, NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'Creating index LRN_CustomerID.'; gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrinter(LRN, CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET NPANXXX = SUBSTRING(cdrinter.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- parse the NPANXX out of the billed number
RAISE NOTICE 'Parsing NPANXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET NPANXX = SUBSTRING(cdrinter.billednumber FROM 3 FOR 6);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx
FROM effectiveinterstateratemaster
WHERE cdrinter.customerid = effectiveinterstateratemaster.customerid AND cdrinter.NPANXXX = effectiveinterstateratemaster.npanxxx;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx 
FROM effectiveinterstateratemaster
WHERE cdrinter.customerid = effectiveinterstateratemaster.customerid AND cdrinter.NPANXX = effectiveinterstateratemaster.npanxxx and cdrinter.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx 
FROM effectiveinterstateratemaster
WHERE  cdrinter.customerid = effectiveinterstateratemaster.customerid AND effectiveinterstateratemaster.npanxxx = '*' AND
cdrinter.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, 'No rate for NPANXX or NPANXXX.'
FROM cdrinter where RetailRate is null; --AND CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'deleting moved records from temp table.';
gentime = TIMEOFDAY();
DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter WHERE RetailRate is null);

DELETE FROM cdrinter WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET CNAMFee = CNAMDipRate FROM customermaster WHERE cdrinter.CNAMDipped = true AND cdrinter.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET LRNDipFee = LRNDipRate FROM customermaster
WHERE trim(both ' ' from LRN) <> '' AND 
not LRN is null AND 
cdrinter.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailPrice + CNAMFee; -- WHERE cdrinter.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailPrice + LRNDipFee; -- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice, RoutingPrefix) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice, RoutingPrefix
FROM cdrinter; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

END;

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnRateInterstateCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

RETURN 00000;

END;



$$ LANGUAGE plpgsql;

