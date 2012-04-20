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


CREATE OR REPLACE FUNCTION "fnCalculateConcurrentCallsDirectionCarrier"()
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
id varchar(100);
curs2 refcursor;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
RAISE NOTICE 'Interval (seconds): %', iSamplingInterval;

-- get datetime of the last calc we did.
SELECT INTO CCDateTime max(CallDateTime) FROM "ConcurrentCallsDirectionCarrier";

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

	OPEN curs2 FOR SELECT distinct (coalesce(CarrierID, '')) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second';

	LOOP
		FETCH curs2 INTO id;
		EXIT WHEN NOT FOUND;
	
		select into iConcurrentCallsIN count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'I' and coalesce(CarrierID, '') = id;

		insert into "ConcurrentCallsDirectionCarrier" (CallDateTime, Direction, CarrierID, ConcurrentCalls)
	                                         values  (CCDateTime, 'I', id, iConcurrentCallsIN);


		select into iConcurrentCallsOUT count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'O' and coalesce(CarrierID, '') = id;

		insert into "ConcurrentCallsDirectionCarrier" (CallDateTime, Direction, CarrierID, ConcurrentCalls)
	                                          values  (CCDateTime, 'O', id, iConcurrentCallsOUT);
	
	
		
	END LOOP;
	close curs2;
	
	CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';

END LOOP;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();


RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO "ProcessHistory" VALUES('fnCalculateConcurrentCallsDirectionCarrier',StartDateTime,EndDateTime,age(EndDateTime,StartDateTime),null);

RETURN 00000;

END;



$BODY$
  LANGUAGE plpgsql;