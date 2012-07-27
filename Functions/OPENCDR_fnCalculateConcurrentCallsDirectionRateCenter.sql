CREATE OR REPLACE FUNCTION "fnCalculateConcurrentCallsDirectionRateCenter"()
  RETURNS integer AS
$BODY$


DECLARE 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CCDateTime timestamp without time zone; 
MaxCCDateTime timestamp without time zone; 
iConcurrentCallsIN int;
iConcurrentCallsOUT int;
iSamplingInterval int = 60; --(seconds). The lower the interval the more accurate it will be but slower as well.
id varchar(50);
curs2 refcursor;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
RAISE NOTICE 'Interval (seconds): %', iSamplingInterval;

-- get datetime of the last calc we did.
SELECT INTO CCDateTime max(CallDateTime) FROM "ConcurrentCallsDirectionRateCenter";

IF CCDateTime IS NULL THEN 
	SELECT INTO CCDateTime min(CallDateTime) FROM "CallRecordMaster";
END IF;

RAISE NOTICE 'Min CDR Date: %', CCDateTime;

IF CCDateTime IS NULL THEN 
	RAISE NOTICE 'No calls in the system.';
	RETURN 1;
END IF;

CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';
RAISE NOTICE 'CDR Date: %', CCDateTime;


SELECT INTO MaxCCDateTime max(CallDateTime) FROM "CallRecordMaster";
RAISE NOTICE 'Max CDR Date: %', MaxCCDateTime;

RAISE NOTICE 'Calculating concurrent calls...';
while CCDateTime <= MaxCCDateTime LOOP

	RAISE NOTICE 'CDR Date: %', CCDateTime;

	OPEN curs2 FOR SELECT distinct (coalesce(RateCenter, '')) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second';

	LOOP
		FETCH curs2 INTO id;
		EXIT WHEN NOT FOUND;
	
		select into iConcurrentCallsIN count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'I' and coalesce(RateCenter, '') = id;

		insert into "ConcurrentCallsDirectionRateCenter" (CallDateTime, Direction, RateCenter, ConcurrentCalls)
	                                         values  (CCDateTime, 'I', id, iConcurrentCallsIN);


		select into iConcurrentCallsOUT count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'O' and coalesce(RateCenter, '') = id;

		insert into "ConcurrentCallsDirectionRateCenter" (CallDateTime, Direction, RateCenter, ConcurrentCalls)
	                                          values  (CCDateTime, 'O', id, iConcurrentCallsOUT);
	
	
		
	END LOOP;
	close curs2;
	
	CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';

END LOOP;


-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();


RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO "ProcessHistory" VALUES('fnCalculateConcurrentCallsDirectionRateCenter',StartDateTime,EndDateTime,age(EndDateTime,StartDateTime),null);

RETURN 00000;

END;



$BODY$
  LANGUAGE plpgsql;