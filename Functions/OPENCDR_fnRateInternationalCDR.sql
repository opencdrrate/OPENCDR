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

CREATE OR REPLACE FUNCTION "fnRateInternationalCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for Inter call type = 25
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 25;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective international Rate Sheet
TRUNCATE TABLE effectiveinternationalratemaster;

INSERT INTO effectiveinternationalratemaster 
SELECT ratesheet.CustomerID, ratesheet.BilledPrefix, RetailRate FROM internationalratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM internationalratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, BilledPrefix) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTERNATIONAL_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime,gentime);

INSERT INTO processhistory VALUES('Generating International Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrinternat (
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
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrinternat (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, retailrate, retailprice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 25
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrinternat.sourceip = ipaddressmaster.ipaddress and coalesce(cdrinternat.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrinternat where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat where coalesce(customerid, '') = '');

DELETE FROM cdrinternat where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrinternat_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrinternat_rawduration ON cdrinternat (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN


RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
DELETE FROM cdrinternat WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrinternat SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--attempt to massage DestinationNumber to E.164 format.
RAISE NOTICE 'Attempt to massage to E.164 format.'; gentime = TIMEOFDAY();
update cdrinternat set destinationnumber = '+' || destinationnumber where substring(destinationnumber from 1 for 1) = '1' and char_length(destinationnumber) = 11;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
	UPDATE cdrinternat SET billedprefix = effectiveinternationalratemaster.billedprefix, retailrate = effectiveinternationalratemaster.retailrate 
	FROM effectiveinternationalratemaster
	WHERE cdrinternat.customerid = effectiveinternationalratemaster.customerid and SUBSTRING(cdrinternat.destinationnumber FROM 1 FOR i) = effectiveinternationalratemaster.billedprefix and cdrinternat.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET retailrate = effectiveinternationalratemaster.retailrate, billedprefix = '*' 
FROM effectiveinternationalratemaster
WHERE  cdrinternat.customerid = effectiveinternationalratemaster.customerid AND effectiveinternationalratemaster.billedprefix = '*' AND
cdrinternat.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, 'No rate found.'
FROM cdrinternat where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat WHERE RetailRate is null);

DELETE FROM cdrinternat WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, wholesalerate, wholesaleprice
FROM cdrinternat; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnRateInternationalCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

