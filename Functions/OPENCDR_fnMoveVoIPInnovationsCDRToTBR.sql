--======================================================================--
/*  OpenCDRRate – Rate your call records.
    Copyright (C) 2012  DTH Software, Inc

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

CREATE OR REPLACE FUNCTION "fnMoveVoIPInnovationsCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_voipinnovations (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from voipinnovationscdr where callduration <= 0;

update voipinnovationscdr set calltype = 25 where calltype = 'TERM_EXT_US_INTER';

update voipinnovationscdr set calltype = 30 where calltype = '800OrigC';

update voipinnovationscdr set calltype = 30 where calltype = '800OrigE';

update voipinnovationscdr set calltype = 15 where calltype = 'Orig-Tiered';

update voipinnovationscdr set calltype = 10 where calltype = 'TERM_INTERSTATE';

update voipinnovationscdr set calltype = 5 where calltype = 'TERM_INTRASTATE';

INSERT INTO tmp_voipinnovations (rowid) SELECT rowid FROM voipinnovationscdr ORDER BY rowid LIMIT PROCESSING_LIMIT;

INSERT INTO callrecordmaster_tbr (callid, customerid, calltype, calldatetime, duration, direction, sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, ratecenter, CarrierID) 
SELECT RowID, '', cast(calltype as smallint), starttime, cast(callduration as int), null, customerip, ani, dnis, lrn, false, origtier, 'VI' FROM voipinnovationscdr WHERE RowID IN (SELECT rowid FROM tmp_voipinnovations);

DELETE FROM voipinnovationscdr WHERE rowid IN (SELECT rowid FROM tmp_voipinnovations);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;