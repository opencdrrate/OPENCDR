CREATE OR REPLACE FUNCTION "fnMoveArettaCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_aretta (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from arettacdr where sec <= 0;

update arettacdr set calltype = 35 where calltype = 'Outbound';

update arettacdr set "CallType" = 15 where "CallType" = 'Inbound';

INSERT INTO tmp_aretta (rowid) SELECT rowid FROM arettacdr ORDER BY rowid LIMIT PROCESSING_LIMIT;

INSERT INTO callrecordmaster_tbr (callid,customerid,calltype,calldatetime,duration,direction,sourceip,originatingnumber,destinationnumber,lrn,cnamdipped,ratecenter, CarrierID) 
SELECT RowID, null, cast(CallType as smallint), CallDate, Sec, null, null, source_callerid, Destination, null, false, 'DEFAULT', trunk FROM arettacdr WHERE RowID IN (SELECT rowid FROM tmp_aretta);

DELETE FROM arettacdr WHERE rowid IN (SELECT rowid FROM tmp_aretta);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;