CREATE OR REPLACE FUNCTION "fnRateRetailTerminationCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for call type = 60
SELECT INTO CDRDate MIN(CallDateTime) FROM callRecordMaster_tbr
WHERE CallType = 60;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;


gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process started at %', gentime;

-- create temporary rates table 
--if you rate often, convert this to a permanent table and have other process update rates as necessary.
CREATE TEMPORARY TABLE retailtermrates (
        PlanID varchar(15) NOT NULL,
        BilledPrefix varchar(10) NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        CanBeFree boolean NOT NULL,
	RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(PlanID, BilledPrefix)
)ON COMMIT DROP;

INSERT INTO retailtermrates
SELECT ratesheet.PlanID, ratesheet.BilledPrefix, ratesheet.RetailRate, ratesheet.CanBeFree FROM retailplanterminationratemaster AS ratesheet
INNER JOIN (SELECT PlanID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM retailplanterminationratemaster WHERE effectivedate <= CDRDate GROUP BY PlanID, BilledPrefix) AS inaffect
ON ratesheet.PlanID = inaffect.PlanID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;


EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime, gentime);

INSERT INTO processhistory VALUES('Generating Retail Termination Rate Sheet', gentime, EndDateTime, age(EndDateTime, gentime), rec_count);


-- create temporary processing table 
CREATE TEMPORARY TABLE cdrretailterm (
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
        PlanID varchar(15),
	BilledPrefix varchar(10), 
	RetailRate numeric(9,7),
	RetailPrice numeric(19,7),
	CarrierID varchar(100),
	RateCenter varchar(50),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
        RoutingPrefix varchar(10),
        CanBeFree boolean NOT NULL
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrretailterm (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, PlanID, billedprefix, retailrate, retailprice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, false
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 60
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find customerid by originating number.
RAISE NOTICE 'Retrieving customerID based on Originating Number.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET customerid = didmaster.customerid 
FROM didmaster
WHERE cdrretailterm.originatingnumber = didmaster.DID and coalesce(cdrretailterm.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrretailterm.sourceip = ipaddressmaster.ipaddress and coalesce(cdrretailterm.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid CustomerID.'
FROM cdrretailterm where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailterm where coalesce(customerid, '') = '');

DELETE FROM cdrretailterm where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrretailterm_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrretailterm_rawduration ON cdrretailterm (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
DELETE FROM cdrretailterm WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrretailterm SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrretailterm SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrretailterm SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrretailterm SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrretailterm SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrretailterm SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find retail plan per customer
RAISE NOTICE 'Retrieving retail plan.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET PlanID = customerretailplanmaster.PlanID
FROM customerretailplanmaster
WHERE cdrretailterm.CustomerID = customerretailplanmaster.CustomerID and coalesce(cdrretailterm.PlanID, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
	UPDATE cdrretailterm SET billedprefix = retailtermrates.billedprefix, retailrate = retailtermrates.retailrate, canbefree = retailtermrates.canbefree 
	FROM retailtermrates
	WHERE cdrretailterm.PlanID = retailtermrates.PlanID and SUBSTRING(cdrretailterm.destinationnumber FROM 1 FOR i) = retailtermrates.billedprefix and cdrretailterm.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET retailrate = retailtermrates.retailrate, billedprefix = '*', canbefree = retailtermrates.canbefree 
FROM retailtermrates
WHERE  cdrretailterm.PlanID = retailtermrates.PlanID AND retailtermrates.billedprefix = '*' AND
cdrretailterm.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, 'No retail termination rate found.'
FROM cdrretailterm where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailterm WHERE RetailRate is null);

DELETE FROM cdrretailterm WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrretailterm SET RetailPrice = RetailRate * BilledDuration / 60.00000;


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree
FROM cdrretailterm; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailterm);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnRateRetailTerminationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

