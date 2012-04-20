--======================================================================--
/*  OpenCDRRate Rate your call records.
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

CREATE OR REPLACE FUNCTION "fnRateTollFreeOriginationCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for Inter call type = 30
SELECT INTO CDRDate MIN(CallDateTime) FROM "callrecordmaster_tbr"
WHERE CallType = 30;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM "systemsettings_date"
WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE') OR (select count(*) FROM "systemsettings_date" WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective international Rate Sheet
TRUNCATE TABLE "effectivetollfreeoriginationratemaster";

INSERT INTO "effectivetollfreeoriginationratemaster" 
SELECT ratesheet.CustomerID, ratesheet.BilledPrefix, RetailRate FROM "tollfreeoriginationratemaster" AS ratesheet
INNER JOIN (SELECT CustomerID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM "tollfreeoriginationratemaster" WHERE effectivedate <= CDRDate GROUP BY CustomerID, BilledPrefix) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM "systemsettings_date" WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE';
INSERT INTO "systemsettings_date" VALUES ('TFORIG_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime,gentime);

INSERT INTO "processhistory" VALUES('Generating TollFree Origination Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrtforig (
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
		BilledPrefix varchar(10), 
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
		CarrierID varchar(100),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrtforig (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, retailrate, retailprice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, CarrierID, wholesalerate, wholesaleprice
FROM "callrecordmaster_tbr" 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 30
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
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET customerid = "ipaddressmaster".customerid 
FROM "ipaddressmaster"
WHERE cdrtforig.sourceip = "ipaddressmaster".ipaddress and cdrtforig.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "callrecordmaster_held" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrtforig where customerid = '' AND CallID not in (SELECT CallID FROM "callrecordmaster_held");

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig where customerid = '');

DELETE FROM cdrtforig where customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrtforig_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrtforig_rawduration ON cdrtforig (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrtforig WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrtforig SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
UPDATE cdrtforig SET billedprefix = "effectivetollfreeoriginationratemaster".billedprefix, retailrate = "effectivetollfreeoriginationratemaster".retailrate 
FROM "effectivetollfreeoriginationratemaster"
WHERE cdrtforig.customerid = "effectivetollfreeoriginationratemaster".customerid and SUBSTRING(cdrtforig.originatingnumber FROM 1 FOR i) = "effectivetollfreeoriginationratemaster".billedprefix and cdrtforig.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET retailrate = "effectivetollfreeoriginationratemaster".retailrate, billedprefix = '*' 
FROM "effectivetollfreeoriginationratemaster"
WHERE  cdrtforig.customerid = "effectivetollfreeoriginationratemaster".customerid AND "effectivetollfreeoriginationratemaster".billedprefix = '*' AND
cdrtforig.retailrate is null;
EndDateTime = TIMEOFDAY(); 
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "callrecordmaster_held" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'No rate found.'
FROM cdrtforig where RetailRate is null AND CallID not in (SELECT CallID FROM "callrecordmaster_held");

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig WHERE RetailRate is null);

DELETE FROM cdrtforig WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster" (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'I', SourceIP, OriginatingNumber, DestinationNumber, null, 0, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, wholesalerate, wholesaleprice
FROM cdrtforig; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Function took %', age(EndDateTime,StartDateTime);

INSERT INTO "processhistory" VALUES('fnRateTollFreeOriginationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

