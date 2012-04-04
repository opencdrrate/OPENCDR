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

CREATE OR REPLACE FUNCTION "fnMoveBandwidthCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
gentime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_bandwidth (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from bandwidthcdr where billable_minutes <= 0;


INSERT INTO tmp_bandwidth (rowid) SELECT rowid FROM bandwidthcdr ORDER BY record_date LIMIT PROCESSING_LIMIT;

--massage source/dest into E.164 if possible
update bandwidthcdr set src = '+1' || src where char_length(src) = 10 and substring(src from 1 for 1) <> '+';
update bandwidthcdr set src = '+' || src where char_length(src) = 11 and substring(src from 1 for 1) = '1';
update bandwidthcdr set dest = '+1' || dest where char_length(dest) = 10 and substring(dest from 1 for 1) <> '+';
update bandwidthcdr set dest = '+' || dest where char_length(dest) = 11 and substring(dest from 1 for 1) = '1';
update bandwidthcdr set dest = '+' || substring(dest from 4 for 20) where substring(dest from 1 for 3) = '011';
--update bandwidthcdr set dest = '+' || dest where usagetype = 'international' and substring(dest from 1 for 1) <> '+';
update bandwidthcdr set lrn = '+1' || lrn where char_length(lrn) = 10 and substring(lrn from 1 for 1) <> '+';
update bandwidthcdr set lrn = '+' || lrn where char_length(lrn) = 11 and substring(lrn from 1 for 1) = '1';
update bandwidthcdr set lrn = '+' || substring(lrn from 4 for 20) where substring(lrn from 1 for 3) = '011';


--try to figure out the customerid/call type by the DID.
RAISE NOTICE 'Attempting to determine customer and call type.'; gentime = TIMEOFDAY();

UPDATE bandwidthcdr SET customerid = didmaster.customerid 
FROM didmaster
WHERE bandwidthcdr.src = didmaster.did and coalesce(bandwidthcdr.customerid, '') = '';

UPDATE bandwidthcdr SET customerid = didmaster.customerid 
FROM didmaster
WHERE bandwidthcdr.dest = didmaster.did and coalesce(bandwidthcdr.customerid, '') = '';


update bandwidthcdr set calltype = 10, direction = 'O' where itemtype = 'INTERSTATE';
update bandwidthcdr set calltype = 5, direction = 'O' where itemtype = 'INTRASTATE';

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



RAISE NOTICE 'Calculating duration.'; gentime = TIMEOFDAY();

update bandwidthcdr set rawduration = billable_minutes * 60.0;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


INSERT INTO callrecordmaster_tbr (callid, customerid, calltype, calldatetime, duration, direction, sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, ratecenter, CarrierID, wholesalerate, wholesaleprice) 
SELECT itemid, customerid, calltype, record_date, rawduration, direction, null, src, dest, lrn, false, dst_rcs, 'BANDWIDTH', trans_rate, amount FROM bandwidthcdr WHERE RowID IN (SELECT rowid FROM tmp_bandwidth);

DELETE FROM bandwidthcdr WHERE rowid IN (SELECT rowid FROM tmp_bandwidth);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;