--======================================================================--
/*  OpenCDRRate – Rate your call records.
    Copyright (C) 2011  DTH Software, Inc

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    See <http://www.gnu.org/licenses/>.                                 */
--======================================================================-- 

CREATE OR REPLACE FUNCTION "fnRateTieredOriginationCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 15
SELECT INTO CDRDate MIN(CallDateTime) FROM "CallRecordMaster_TBR"
WHERE CallType = 15;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrorig (
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN      varchar(50),
		LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
		BilledNumber varchar(50), 
		NPANXXX varchar(7),
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
        CNAMDipped boolean,
		CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
		BilledTier smallint,
		RateCenter varchar(50),
		CarrierID varchar(100),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrorig (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, NPANXXX, RetailRate, RetailPrice, CNAMDipped, CNAMFee, BilledTier, RateCenter, CarrierID, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, 0, null, null, null, null, CNAMdipped, 0, null, RateCenter, CarrierID, wholesalerate, wholesaleprice
FROM "CallRecordMaster_TBR" 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 15
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET customerid = "IPAddressMaster".customerid 
FROM "IPAddressMaster"
WHERE cdrorig.sourceip = "IPAddressMaster".ipaddress and cdrorig.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster_HELD" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrorig where customerid = '' AND CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");

DELETE FROM "CallRecordMaster_TBR" WHERE CallID in (SELECT CallID FROM cdrorig where customerid = '');

DELETE FROM cdrorig where customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);




CREATE INDEX cdrorig_rawduration ON cdrorig (Duration);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrorig SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- find the tier for each call based on rate center
RAISE NOTICE 'Finding the tier for each call based on rate center.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET BilledTier = "TieredOriginationRateCenterMaster".tier 
FROM "TieredOriginationRateCenterMaster"
WHERE cdrorig.ratecenter = "TieredOriginationRateCenterMaster".ratecenter;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the tier for
RAISE NOTICE 'Moving records with no tier to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster_HELD" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, 'Invalid Rate Center'
FROM cdrorig where BilledTier is null;

DELETE FROM "CallRecordMaster_TBR" WHERE CallID in (SELECT callid FROM cdrorig WHERE BilledTier is null);

DELETE FROM cdrorig WHERE BilledTier is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- create a de-versioned rate sheet based on call date
RAISE NOTICE 'create a de-versioned rate sheet based on call date.';
CREATE TEMPORARY TABLE origrates(
	CustomerID varchar(50) NOT NULL,
	Tier smallint NOT NULL,
	RetailRate numeric(9, 7) NOT NULL,
PRIMARY KEY (CustomerID, Tier)
) ON COMMIT DROP;

INSERT INTO origrates (CustomerID, Tier, Retailrate)
SELECT ratesheet.CustomerID, ratesheet.Tier, ratesheet.RetailRate FROM "TieredOriginationRateMaster" AS ratesheet INNER JOIN (SELECT CustomerID, Tier, MAX(EffectiveDate) AS EffectiveDate FROM "TieredOriginationRateMaster" WHERE EffectiveDate <= CDRDate GROUP BY CustomerID, Tier) AS inaffect ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.Tier = inaffect.Tier AND ratesheet.EffectiveDate = inaffect.EffectiveDate;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get rate by tier
RAISE NOTICE 'Get rate by tier.';
UPDATE cdrorig SET RetailRate =  origrates.RetailRate FROM origrates WHERE cdrorig.CustomerID = origrates.CustomerID AND cdrorig.BilledTier = origrates.Tier;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move to HELD if there is no Tier rate found
RAISE NOTICE 'Moving records with no tier rate to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster_HELD" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, 'No Tier Rate Found'
FROM cdrorig WHERE RetailRate is null;

DELETE FROM "CallRecordMaster_TBR" WHERE CallID in (SELECT callid FROM cdrorig WHERE RetailRate is null);

DELETE FROM cdrorig WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculate retail price.';
UPDATE cdrorig SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster" (CallID, CustomerID, CallType, CallDateTime,  Duration, BilledDuration, Direction, SourceIP, OriginatingNumber,  DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, BilledTier, RateCenter, RetailPrice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime,  Duration, BilledDuration, 'I', SourceIP, OriginatingNumber,  DestinationNumber, LRN, LRNDipFee, OriginatingNumber, CURRENT_TIMESTAMP, RetailRate, CNAMDipped, CNAMFee, BilledTier, RateCenter, RetailPrice, CarrierID, wholesalerate, wholesaleprice FROM cdrorig;

DELETE FROM "CallRecordMaster_TBR" WHERE CallID in (SELECT callid FROM cdrorig);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO "ProcessHistory" VALUES('fnRateTieredOriginationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

RETURN 00000;

END;





$$ LANGUAGE plpgsql;
