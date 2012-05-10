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

CREATE OR REPLACE FUNCTION "fnCategorizeCDR"() RETURNS int AS $$

DECLARE
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;


--cleanup
delete from callrecordmaster_tbr where Duration <= 0;
update callrecordmaster_tbr set customerid = replace(customerid, ' ', '');


--massages source/dest into E.164
--NANPA
update callrecordmaster_tbr set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update callrecordmaster_tbr set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update callrecordmaster_tbr set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) not in ('+', '0');
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';

--UK
update callrecordmaster_tbr set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 12 and substring(OriginatingNumber from 1 for 2) = '44';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 2) = '44';


--UK Geographic
update callrecordmaster_tbr set DestinationNumber = '+44' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 3) = '901';
update callrecordmaster_tbr set DestinationNumber = '+44' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 3) = '902';

--UK Mobile
update callrecordmaster_tbr set DestinationNumber = '+44' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 3) = '907';

--UK Other
update callrecordmaster_tbr set DestinationNumber = '+44' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 3) = '908';


--try to figure out the customerid/direction by the DID.
RAISE NOTICE 'Attempting to determine customer and direction'; gentime = TIMEOFDAY();

UPDATE callrecordmaster_tbr SET CustomerID = didmaster.customerid 
FROM didmaster
WHERE callrecordmaster_tbr.OriginatingNumber = didmaster.did and coalesce(callrecordmaster_tbr.CustomerID, '') = '';

UPDATE callrecordmaster_tbr SET direction = 'O' 
FROM didmaster
WHERE callrecordmaster_tbr.OriginatingNumber = didmaster.did and coalesce(callrecordmaster_tbr.direction, '') = '';

UPDATE callrecordmaster_tbr SET CustomerID = didmaster.customerid 
FROM didmaster
WHERE callrecordmaster_tbr.DestinationNumber = didmaster.did and coalesce(callrecordmaster_tbr.customerid, '') = '';

UPDATE callrecordmaster_tbr SET direction = 'I' 
FROM didmaster
WHERE callrecordmaster_tbr.DestinationNumber = didmaster.did and coalesce(callrecordmaster_tbr.direction, '') = '';

UPDATE callrecordmaster_tbr SET CustomerID = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE callrecordmaster_tbr.sourceip = ipaddressmaster.ipaddress and coalesce(callrecordmaster_tbr.customerid, '') = '';

UPDATE callrecordmaster_tbr SET direction = 'O' 
FROM ipaddressmaster
WHERE callrecordmaster_tbr.sourceip = ipaddressmaster.ipaddress and coalesce(callrecordmaster_tbr.direction, '') = '';


EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



RAISE NOTICE 'Flagging international calls.'; gentime = TIMEOFDAY();
--add code here to assign call type 25 for international NANPA outbound destinations
update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 5) in ('+1242', '+1246', '+1264', '+1268', '+1284', '+1340', '+1345', '+1441', '+1473', '+1649', '+1664', '+1758', '+1767', '+1784', '+1787', '+1809', '+1829', '+1849', '+1868', '+1869', '+1876');

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '00';

/*south africa*/
update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 3 for 12) where CallType = 25
    and substring(DestinationNumber from 1 for 2) = '00' and char_length(DestinationNumber) = 14;
    

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




--Attempting to determine whether domestic calls are inter/intra.
RAISE NOTICE 'Attempting to determine whether domestic calls are inter/intra.'; gentime = TIMEOFDAY();

CREATE TEMPORARY TABLE cdr (
        CallID  varchar(100)     PRIMARY KEY,
        SourceNPA char(3) NOT NULL,
        DestNPA char(3) NOT NULL,
        SourceState varchar(50),
        DestState varchar(50)
)ON COMMIT DROP;


insert into cdr (CallID, SourceNPA, DestNPA, SourceState, DestState)
	select CallID, substring(callrecordmaster_tbr.OriginatingNumber from 3 for 3), substring(callrecordmaster_tbr.DestinationNumber from 3 for 3), null, null
	 from callrecordmaster_tbr 
		where callrecordmaster_tbr.CallType is null 
	      and callrecordmaster_tbr.Direction = 'O' 
	      and char_length(callrecordmaster_tbr.OriginatingNumber) = 12
	      and substring(callrecordmaster_tbr.OriginatingNumber from 1 for 2) = '+1'
	      and char_length(callrecordmaster_tbr.DestinationNumber) = 12
	      and substring(callrecordmaster_tbr.DestinationNumber from 1 for 2) = '+1';



UPDATE cdr SET SourceState = npamaster.state 
FROM npamaster
WHERE cdr.SourceNPA = npamaster.npa;


UPDATE cdr SET DestState = npamaster.state 
FROM npamaster
WHERE cdr.DestNPA = npamaster.npa;



update callrecordmaster_tbr set CallType = 5 where CallID in (select CallID from cdr where SourceState = DestState and not SourceState is null);

update callrecordmaster_tbr set CallType = 10 where CallID in (select CallID from cdr where SourceState <> DestState and not SourceState is null);


/*south africa*/
update callrecordmaster_tbr set CallType = 35 where CallType is null and Direction = 'O' and char_length(DestinationNumber) in (10, 11) and substring(DestinationNumber from 1 for 1) = '0';


EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;