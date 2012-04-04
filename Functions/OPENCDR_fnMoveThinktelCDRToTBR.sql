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

CREATE OR REPLACE FUNCTION "fnMoveThinktelCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
gentime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_thinktel (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from thinktelcdr where rawduration <= 0;


INSERT INTO tmp_thinktel (rowid) SELECT rowid FROM thinktelcdr ORDER BY calldate LIMIT PROCESSING_LIMIT;

--massages source/dest into E.164
update thinktelcdr set sourcenumber = '+1' || sourcenumber where char_length(sourcenumber) = 10 and substring(sourcenumber from 1 for 1) <> '+';
update thinktelcdr set sourcenumber = '+' || sourcenumber where char_length(sourcenumber) = 11 and substring(sourcenumber from 1 for 1) = '1';
update thinktelcdr set destinationnumber = '+1' || destinationnumber where char_length(destinationnumber) = 10 and substring(destinationnumber from 1 for 1) <> '+';
update thinktelcdr set destinationnumber = '+' || destinationnumber where char_length(destinationnumber) = 11 and substring(destinationnumber from 1 for 1) = '1';
update thinktelcdr set destinationnumber = '+' || substring(destinationnumber from 4 for 20) where substring(destinationnumber from 1 for 3) = '011';
update thinktelcdr set destinationnumber = '+' || destinationnumber where usagetype = 'international' and substring(destinationnumber from 1 for 1) <> '+';


--try to figure out the customerid/call type by the DID.
RAISE NOTICE 'Attempting to determine customer and call type.'; gentime = TIMEOFDAY();

UPDATE thinktelcdr SET customerid = didmaster.customerid 
FROM didmaster
WHERE thinktelcdr.sourcenumber = didmaster.did and coalesce(thinktelcdr.customerid, '') = '';

UPDATE thinktelcdr SET customerid = didmaster.customerid 
FROM didmaster
WHERE thinktelcdr.destinationnumber = didmaster.did and coalesce(thinktelcdr.customerid, '') = '';

update thinktelcdr set calltype = 15, direction = 'I' where usagetype = 'incoming';
update thinktelcdr set calltype = 30, direction = 'I' where usagetype = 'tfin';
update thinktelcdr set calltype = 25, direction = 'O' where usagetype = 'international';
update thinktelcdr set calltype = 35, direction = 'O' where usagetype = 'local';



EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


INSERT INTO callrecordmaster_tbr (callid, customerid, calltype, calldatetime, duration, direction, sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, ratecenter, CarrierID) 
SELECT calldate || '_' || sourcenumber || '_' || destinationnumber || '_' || rawduration, customerid, calltype, calldate, rawduration, direction, null, sourcenumber, destinationnumber, null, false, null, 'THINKTEL' FROM thinktelcdr WHERE RowID IN (SELECT rowid FROM tmp_thinktel);

DELETE FROM thinktelcdr WHERE rowid IN (SELECT rowid FROM tmp_thinktel);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;
