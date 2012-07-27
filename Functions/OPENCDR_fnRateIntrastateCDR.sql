CREATE OR REPLACE FUNCTION "fnRateIntrastateCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for intra call type = 5
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 5;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective Term Rate Sheet
TRUNCATE TABLE effectiveintrastateratemaster;

INSERT INTO effectiveintrastateratemaster 
SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM intrastateratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM intrastateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTRA_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed in %', age(EndDateTime,gentime);

INSERT INTO processhistory VALUES('Generating Intra Rate Sheet',gentime, EndDateTime, age(EndDateTime, gentime), rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrintra (
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
	CarrierID varchar(100),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
        RoutingPrefix varchar(10),
	RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.';
gentime = TIMEOFDAY();

INSERT INTO cdrintra (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, lrn, lrndipfee, billednumber, npanxxx, npanxx, retailrate, retailprice, cnamdipped, cnamfee, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, LRN, 0, null, null, null, null, null, CNAMdipped, 0, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix
FROM callrecordmaster_tbr
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 5
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
EndDateTime = TIMEOFDAY();
RAISE NOTICE '% new records to be rated. Gathered in: %', rec_count, age(EndDateTime,gentime) ;
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--try to figure out the customerid/by the DID.
RAISE NOTICE 'Attempting to determine customer'; gentime = TIMEOFDAY();
UPDATE cdrintra SET CustomerID = didmaster.customerid 
FROM didmaster
WHERE cdrintra.OriginatingNumber = didmaster.did and coalesce(cdrintra.CustomerID, '') = '';


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrintra SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrintra.sourceip = ipaddressmaster.ipaddress and coalesce(cdrintra.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid CustomerID.'
FROM cdrintra where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra where coalesce(customerid, '') = '');

DELETE FROM cdrintra where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);




-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrintra_rawduration.';
gentime = TIMEOFDAY();
CREATE INDEX cdrintra_rawduration ON cdrintra (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN

-- delete 0 duration
RAISE NOTICE 'Deleting 0 duration.';
gentime = TIMEOFDAY();
DELETE FROM cdrintra WHERE duration <= 0;
DELETE FROM callrecordmaster_tbr WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrintra SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--massage source/dest into E.164 if possible
update cdrintra set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update cdrintra set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update cdrintra set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) <> '+';
update cdrintra set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update cdrintra set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';
update cdrintra set LRN = '+1' || lrn where char_length(LRN) = 10 and substring(LRN from 1 for 1) <> '+';
update cdrintra set LRN = '+' || lrn where char_length(LRN) = 11 and substring(LRN from 1 for 1) = '1';
update cdrintra set LRN = '+' || substring(LRN from 4 for 20) where substring(LRN from 1 for 3) = '011';


-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrintra SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrintra SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- filter out BilledNumbers with invalid format 
RAISE NOTICE 'filter out BilledNumbers with invalid format to HELD table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid BilledNumber Format. Must be E.164.'
FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1';

DELETE FROM callrecordmaster_tbr WHERE callid IN (SELECT CallID FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1');

DELETE FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET NPANXXX = SUBSTRING(cdrintra.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXX out of the billed number
RAISE NOTICE 'Parsing NPANXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET NPANXX = SUBSTRING(cdrintra.billednumber FROM 3 FOR 6);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




RAISE NOTICE 'Creating index CustomerID_NPANXXX.';
gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrintra(CustomerID, NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


RAISE NOTICE 'Creating index LRN_CustomerID.';
gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrintra(LRN, CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

--RAISE NOTICE 'Analyzing temporary table. %', TIMEOFDAY();
--ANALYZE cdrintra;


-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx 
FROM effectiveintrastateratemaster
WHERE cdrintra.customerid = effectiveintrastateratemaster.customerid AND cdrintra.NPANXXX = effectiveintrastateratemaster.npanxxx;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx 
FROM effectiveintrastateratemaster
WHERE cdrintra.customerid = effectiveintrastateratemaster.customerid AND cdrintra.NPANXX = effectiveintrastateratemaster.npanxxx and cdrintra.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- get default retail rates
RAISE NOTICE 'Getting default rates.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx  
FROM effectiveintrastateratemaster
WHERE  cdrintra.customerid = effectiveintrastateratemaster.customerid AND effectiveintrastateratemaster.npanxxx = '*' AND
cdrintra.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix, 'No rate for NPANXX or NPANXXX.'
FROM cdrintra where RetailRate is null; -- AND CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'deleting moved records from temp table.';
gentime = TIMEOFDAY();
DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra WHERE RetailRate is null);

DELETE FROM cdrintra WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET CNAMFee = CNAMDipRate FROM customermaster WHERE cdrintra.CNAMDipped = true AND cdrintra.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET LRNDipFee = LRNDipRate FROM customermaster 
WHERE trim(both ' ' from LRN) <> '' AND 
not LRN is null AND 
cdrintra.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate retail price
RAISE NOTICE 'Calculating retail price.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailPrice + CNAMFee; -- WHERE cdrintra.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailPrice + LRNDipFee; -- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix,current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice, RoutingPrefix
FROM cdrintra; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnRateIntrastateCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;



$$ LANGUAGE plpgsql;

