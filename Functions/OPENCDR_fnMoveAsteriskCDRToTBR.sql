CREATE OR REPLACE FUNCTION "fnMoveAsteriskCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
gentime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_asterisk (
        rowid int4     PRIMARY KEY
)ON COMMIT DROP;

delete from asteriskcdr where billsec <= 0;

delete from asteriskcdr where disposition = 'NO ANSWER';


INSERT INTO tmp_asterisk (rowid) SELECT rowid FROM asteriskcdr ORDER BY answer LIMIT PROCESSING_LIMIT;

--massages source/dest into E.164
update asteriskcdr set src = '+1' || src where char_length(src) = 10 and substring(src from 1 for 1) <> '+';
update asteriskcdr set src = '+' || src where char_length(src) = 11 and substring(src from 1 for 1) = '1';
update asteriskcdr set dst = '+1' || dst where char_length(dst) = 10 and substring(dst from 1 for 1) <> '+';
update asteriskcdr set dst = '+' || dst where char_length(dst) = 11 and substring(dst from 1 for 1) = '1';
update asteriskcdr set dst = '+' || substring(dst from 4 for 20) where substring(dst from 1 for 3) = '011';


--try to figure out the customerid/call type by the DID.
RAISE NOTICE 'Attempting to determine customer and call type.'; gentime = TIMEOFDAY();

UPDATE asteriskcdr SET accountcode = didmaster.customerid 
FROM didmaster
WHERE asteriskcdr.src = didmaster.did and coalesce(asteriskcdr.accountcode, '') = '';

UPDATE asteriskcdr SET calltype = 35, direction = 'O' 
FROM didmaster
WHERE asteriskcdr.src = didmaster.did;

UPDATE asteriskcdr SET accountcode = didmaster.customerid 
FROM didmaster
WHERE asteriskcdr.dst = didmaster.did and coalesce(asteriskcdr.customerid, '') = '';

UPDATE asteriskcdr SET calltype = 15, direction = 'I' 
FROM didmaster
WHERE asteriskcdr.dst = didmaster.did;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


INSERT INTO callrecordmaster_tbr (callid, customerid, calltype, calldatetime, duration, direction, sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, ratecenter, CarrierID) 
SELECT uniqueid, accountcode, calltype, answer, billsec, direction, null, src, dst, null, false, null, null FROM asteriskcdr WHERE RowID IN (SELECT rowid FROM tmp_asterisk);

DELETE FROM asteriskcdr WHERE rowid IN (SELECT rowid FROM tmp_asterisk);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;