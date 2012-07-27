CREATE OR REPLACE FUNCTION "fnRateRetailOriginationTollFreeCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Limit of how many CDR will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many CDR were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for call type = 68
SELECT INTO CDRDate MIN(CallDateTime) FROM callRecordMaster_tbr
WHERE CallType = 68;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;


-- create temporary processing table 
CREATE TEMPORARY TABLE cdrretailorigtf (
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

INSERT INTO cdrretailorigtf (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, PlanID, billedprefix, retailrate, retailprice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, 0
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 68
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
UPDATE cdrretailorigtf SET customerid = didmaster.customerid 
FROM didmaster
WHERE cdrretailorigtf.destinationnumber = didmaster.DID and coalesce(cdrretailorigtf.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, RoutingPrefix, 'Invalid CustomerID.'
FROM cdrretailorigtf where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorigtf where coalesce(customerid, '') = '');

DELETE FROM cdrretailorigtf where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrretailorigtf_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrretailorigtf_rawduration ON cdrretailorigtf (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
DELETE FROM cdrretailorigtf WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrretailorigtf SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrretailorigtf SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrretailorigtf SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrretailorigtf SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrretailorigtf SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrretailorigtf SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrretailorigtf SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find retail plan per customer
RAISE NOTICE 'Retrieving retail plan.'; gentime = TIMEOFDAY();
UPDATE cdrretailorigtf SET PlanID = customerretailplanmaster.PlanID
FROM customerretailplanmaster
WHERE cdrretailorigtf.CustomerID = customerretailplanmaster.CustomerID and coalesce(cdrretailorigtf.PlanID, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();


UPDATE cdrretailorigtf SET retailrate = retailplanmaster.TollFreeOriginationRate 
FROM retailplanmaster
WHERE cdrretailorigtf.PlanID = retailplanmaster.PlanID and cdrretailorigtf.retailrate is NULL;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, 'No retail toll-free origination rate found.'
FROM cdrretailorigtf where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorigtf WHERE RetailRate is null);

DELETE FROM cdrretailorigtf WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrretailorigtf SET RetailPrice = RetailRate * BilledDuration / 60.00000;


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'I', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice, RoutingPrefix, canbefree
FROM cdrretailorigtf; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrretailorigtf);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnRateRetailOriginationTollFreeCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

