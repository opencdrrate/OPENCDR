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

CREATE OR REPLACE FUNCTION "fnRateIndeterminateJurisdictionCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime TIMESTAMP;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 20
SELECT INTO CDRDate MIN(CallDateTime) FROM "CallRecordMaster_TBR"
WHERE CallType = 20;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrindjur (
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
		WholesaleRate numeric (19, 7),
		WholesalePrice numeric(19, 7),
		RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrindjur (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, Destinationnumber, LRN, LRNDipFee, BilledNumber, NPANXXX, RetailRate, RetailPrice, CNAMDipped, CNAMFee, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, 0, null, null, null, null, CNAMdipped, 0, wholesalerate, wholesaleprice
FROM "CallRecordMaster_TBR" 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 20
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Creating index cdrindjur_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrindjur_rawduration ON cdrindjur (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrindjur WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrindjur SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrindjur SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrindjur SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET NPANXXX = SUBSTRING(cdrindjur.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


RAISE NOTICE 'Creating index CustomerID_NPANXXX.'; gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrindjur(CustomerID,NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


RAISE NOTICE 'Creating index LRN_CustomerID.'; gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrindjur(LRN,CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET CNAMFee = CNAMDipRate FROM "CustomerMaster" WHERE cdrindjur.CNAMDipped = true AND cdrindjur.CustomerID = "CustomerMaster".CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET LRNDipFee = LRNDipRate FROM "CustomerMaster" 
WHERE trim(both ' ' from LRN) <> '' AND 
LRN is not null AND 
cdrindjur.CustomerID = "CustomerMaster".CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Regenerating effective sheet'; gentime = TIMEOFDAY();
-- Regenerate intra effective sheet if any customers in this batch have 5 as their IndeterminateJurisdictionCallType
IF EXISTS(SELECT * from cdrindjur where CustomerID in (SELECT CustomerID FROM "CustomerMaster" WHERE IndeterminateJurisdictionCallType = 5)) THEN

	IF CDRDate <> (SELECT SettingValue FROM "SystemSettings_Date"
WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') OR (select count(*) FROM "SystemSettings_Date" WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') = 0
THEN
		TRUNCATE TABLE "EffectiveIntrastateRateMaster";

		gentime = TIMEOFDAY();
		RAISE NOTICE 'Generating new intra effective rates. This may take several minutes. Process Started At %', gentime;

		INSERT INTO "EffectiveIntrastateRateMaster" 
		SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM "IntrastateRateMaster" AS ratesheet
		INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM "IntrastateRateMaster" WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
		ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect.EffectiveDate;
		GET DIAGNOSTICS rec_count = ROW_COUNT;

		DELETE FROM "SystemSettings_Date" WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE';
		INSERT INTO "SystemSettings_Date" VALUES ('INTRA_EFFECTIVE_RATE_DATE', CDRDate);
		EndDateTime = timeofday();
		RAISE NOTICE 'Generation of new rates completed at %', EndDateTime;
		INSERT INTO "ProcessHistory" VALUES('Generating Intra Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
	END IF;
end if;

-- Regenerate inter effective sheet if any customers have 10 as their IndeterminateJurisdictionCallType
IF EXISTS(SELECT * from cdrindjur where CustomerID in (SELECT CustomerID FROM "CustomerMaster" WHERE IndeterminateJurisdictionCallType =10)) THEN

	IF CDRDate <> (SELECT SettingValue FROM "SystemSettings_Date"
	WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') OR (select count(*) FROM "SystemSettings_Date" WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') = 0
	THEN
			TRUNCATE TABLE "EffectiveInterstateRateMaster";

			gentime = TIMEOFDAY();
			RAISE NOTICE 'Generating new inter effective rates. This may take several minutes. Process Started At %', gentime;

			INSERT INTO "EffectiveInterstateRateMaster" 
			SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM "InterstateRateMaster" AS ratesheet
			INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM "InterstateRateMaster" WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
			ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect.EffectiveDate;
			GET DIAGNOSTICS rec_count = ROW_COUNT;

			DELETE FROM "SystemSettings_Date" WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE';
			INSERT INTO "SystemSettings_Date" VALUES ('INTER_EFFECTIVE_RATE_DATE', CDRDate);
			EndDateTime = timeofday();
			RAISE NOTICE 'Generation of new rates completed at %', EndDateTime;
			INSERT INTO "ProcessHistory" VALUES('Generating Inter Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
	END IF;
end IF;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);
		-- get retail rates
		-- INTRA
		RAISE NOTICE 'Getting intra retail rates.'; gentime = TIMEOFDAY();
		UPDATE cdrindjur SET retailrate = "EffectiveIntrastateRateMaster".retailrate 
		FROM "EffectiveIntrastateRateMaster"
		WHERE cdrindjur.customerid = "EffectiveIntrastateRateMaster".customerid AND cdrindjur.NPANXXX = "EffectiveIntrastateRateMaster".npanxxx AND cdrindjur.CustomerID in 	(select CustomerID from "CustomerMaster" WHERE IndeterminateJurisdictionCallType = 5);
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);	

		-- INTER
				RAISE NOTICE 'Getting inter retail rates.'; gentime = TIMEOFDAY();
		UPDATE cdrindjur SET retailrate = "EffectiveInterstateRateMaster".retailrate 
		FROM "EffectiveInterstateRateMaster"
		WHERE cdrindjur.customerid = "EffectiveInterstateRateMaster".customerid AND cdrindjur.NPANXXX = "EffectiveInterstateRateMaster".npanxxx AND cdrindjur.CustomerID in 	(select CustomerID from "CustomerMaster" WHERE IndeterminateJurisdictionCallType =10);
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

		-- move all records to HELD that we could not find the rate for
		RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
		INSERT INTO "CallRecordMaster_HELD" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
		SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, 'No Rate for NPANXXX'
		FROM cdrindjur where cdrindjur.RetailRate is null AND cdrindjur.CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");

		DELETE FROM "CallRecordMaster_TBR" WHERE "CallRecordMaster_TBR".CallID in (SELECT CallID FROM cdrindjur WHERE RetailRate is null);

		DELETE FROM cdrindjur WHERE RetailRate is null;
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailPrice + CNAMFee; 
-- WHERE cdrindjur.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailPrice + LRNDipFee; 
-- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster" (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice
FROM cdrindjur; 
-- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM "CallRecordMaster_TBR" WHERE CallID in (SELECT CallID FROM cdrindjur);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO "ProcessHistory" VALUES('fnRateIndeterminateJurisdictionCDR', StartDateTime, EndDateTime,age(EndDateTime, StartDateTime), rec_count);




RETURN 00000;

END;





$$ LANGUAGE plpgsql;
