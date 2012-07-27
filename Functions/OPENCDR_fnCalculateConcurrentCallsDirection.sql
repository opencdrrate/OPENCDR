CREATE OR REPLACE FUNCTION "fnCalculateConcurrentCallsDirection"()
  RETURNS integer AS
$BODY$


DECLARE 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CCDateTime timestamp without time zone; 
MaxCCDateTime timestamp without time zone; 
gentime timestamp;
iConcurrentCallsIN int;
iConcurrentCallsOUT int;
iSamplingInterval int = 60; --(seconds). The lower the interval the more accurate it will be but slower as well.

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
RAISE NOTICE 'Interval (seconds): %', iSamplingInterval;

-- get datetime of the last calc we did.
SELECT INTO CCDateTime max(CallDateTime) FROM concurrentcallsdirection;

IF CCDateTime IS NULL THEN 
	SELECT INTO CCDateTime min(CallDateTime) FROM callrecordmaster;
END IF;

RAISE NOTICE 'Min CDR Date: %', CCDateTime;

IF CCDateTime IS NULL THEN 
	RAISE NOTICE 'No calls in the system.';
	RETURN 1;
END IF;

CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';
RAISE NOTICE 'CDR Date: %', CCDateTime;


SELECT INTO MaxCCDateTime max(CallDateTime) FROM callrecordmaster;
RAISE NOTICE 'Max CDR Date: %', MaxCCDateTime;

RAISE NOTICE 'Calculating concurrent calls...';
while CCDateTime <= MaxCCDateTime LOOP

	RAISE NOTICE 'CDR Date: %', CCDateTime;

	select into iConcurrentCallsIN count(*) from callrecordmaster where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'I';

	insert into concurrentcallsdirection (CallDateTime, Direction, ConcurrentCalls)
	                               values  (CCDateTime, 'I', iConcurrentCallsIN);


	select into iConcurrentCallsOUT count(*) from callrecordmaster where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'O';

	insert into concurrentcallsdirection (CallDateTime, Direction, ConcurrentCalls)
	                               values  (CCDateTime, 'O', iConcurrentCallsOUT);
	
	CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';

END LOOP;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();


RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnCalculateConcurrentCallsDirection', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), null);

RETURN 00000;

END;



$BODY$
  LANGUAGE plpgsql;