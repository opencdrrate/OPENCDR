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

CREATE OR REPLACE FUNCTION "fnMoveVitelityCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
gentime timestamp;
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_vitelity (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from vitelitycdr where seconds <= 0;


INSERT INTO tmp_vitelity (rowid) SELECT rowid FROM vitelitycdr ORDER BY calldatetime LIMIT PROCESSING_LIMIT;


--massages source/dest into E.164
update vitelitycdr set source = '+1' || source where char_length(source) = 10 and substring(source from 1 for 1) <> '+';
update vitelitycdr set source = '+' || source where char_length(source) = 11 and substring(source from 1 for 1) = '1';
update vitelitycdr set destination = '+1' || destination where char_length(destination) = 10 and substring(destination from 1 for 1) <> '+';
update vitelitycdr set destination = '+' || destination where char_length(destination) = 11 and substring(destination from 1 for 1) = '1';
update vitelitycdr set destination = '+' || substring(destination from 4 for 20) where substring(destination from 1 for 3) = '011';


--try to figure our the customerid by the DID.
RAISE NOTICE 'Attempting to determine customer.'; gentime = TIMEOFDAY();

UPDATE vitelitycdr SET customerid = didmaster.customerid, calltype = 35, direction = 'O' 
FROM didmaster
WHERE vitelitycdr.source = didmaster.did and coalesce(vitelitycdr.customerid, '') = '';
EndDateTime = TIMEOFDAY();

UPDATE vitelitycdr SET customerid = didmaster.customerid, calltype = 15, direction = 'I' 
FROM didmaster
WHERE vitelitycdr.destination = didmaster.did and coalesce(vitelitycdr.customerid, '') = '';
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



INSERT INTO callrecordmaster_tbr (callid, customerid, calltype, calldatetime, duration, direction, sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, ratecenter, CarrierID) 
SELECT calldatetime || '_' || source || '_' || destination || '_' || seconds, customerid, calltype, calldatetime, seconds, direction, null, source, destination, null, false, null, 'Vitelity' FROM vitelitycdr WHERE RowID IN (SELECT rowid FROM tmp_vitelity);

DELETE FROM vitelitycdr WHERE rowid IN (SELECT rowid FROM tmp_vitelity);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;