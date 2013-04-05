CREATE OR REPLACE FUNCTION "fnRateRetailOriginationCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for call type = 65
SELECT INTO CDRDate MIN(CallDateTime) FROM callRecordMaster_tbr
WHERE CallType = 65;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;


-- create temporary processing table 
CREATE TEMPORARY TABLE cdrretailorig (
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
        CanBeFree boolean NOT NULL,
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The limit can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrretailorig (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, PlanID, billedprefix, retailrate, retailprice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, true
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 65
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
RAISE NOTICE 'Retrieving customerID based on Destination Number.'; gentime = TIMEOFDAY();
UPDATE cdrretailorig SET customerid = didmaster.customerid 
FROM didmaster
WHERE cdrretailorig.destinationnumber = didmaster.DID and coalesce(cdrretailorig.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid CustomerID.'
FROM cdrretailorig where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorig where coalesce(customerid, '') = '');

DELETE FROM cdrretailorig where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrretailorig_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrretailorig_rawduration ON cdrretailorig (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
DELETE FROM cdrretailorig WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrretailorig SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrretailorig SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrretailorig SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrretailorig SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrretailorig SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrretailorig SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrretailorig SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find retail plan per customer
RAISE NOTICE 'Retrieving retail plan.'; gentime = TIMEOFDAY();
UPDATE cdrretailorig SET PlanID = customerretailplanmaster.PlanID
FROM customerretailplanmaster
WHERE cdrretailorig.CustomerID = customerretailplanmaster.CustomerID and coalesce(cdrretailorig.PlanID, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();


UPDATE cdrretailorig SET retailrate = retailplanmaster.OriginationRate 
FROM retailplanmaster
WHERE cdrretailorig.PlanID = retailplanmaster.PlanID and cdrretailorig.retailrate is NULL;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, 'No retail origination rate found.'
FROM cdrretailorig where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorig WHERE RetailRate is null);

DELETE FROM cdrretailorig WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrretailorig SET RetailPrice = RetailRate * BilledDuration / 60.00000;


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'I', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree
FROM cdrretailorig; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorig);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnRateRetailOriginationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

