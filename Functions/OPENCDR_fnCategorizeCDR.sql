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
delete from callrecordmaster_tbr where DestinationNumber = 'congestion';
delete from callrecordmaster_tbr where DestinationNumber in ('s', 'i', 'hangup') and coalesce(CarrierID, '') = '';
update callrecordmaster_tbr set RateCenter = 'VOICEMAIL', Direction = 'I' where char_length(DestinationNumber) = 7 and substring(DestinationNumber from 1 for 3) = 'vmu' and coalesce(RateCenter, '') = '';
update callrecordmaster_tbr set RateCenter = 'VOICEMAIL', Direction = 'I' where DestinationNumber in ('*97', '*98') and coalesce(RateCenter, '') = '';
update callrecordmaster_tbr set RateCenter = 'VOICEMAIL', Direction = 'I' where OriginatingNumber = DestinationNumber and coalesce(RateCenter, '') = '';
update callrecordmaster_tbr set RateCenter = 'MUSICONHOLD', Direction = 'I' where DestinationNumber = 'musiconhold' and coalesce(RateCenter, '') = '';

update callrecordmaster_tbr set DestinationNumber = substring(DestinationNumber from 4 for 4) where RateCenter = 'VOICEMAIL' and char_length(DestinationNumber) = 7 and substring(DestinationNumber from 1 for 3) = 'vmu';


--parse routing prefix
update callrecordmaster_tbr set RoutingPrefix = substring(DestinationNumber from 1 for 1), DestinationNumber = substring(DestinationNumber from 3 for 20) where substring(DestinationNumber from 2 for 1) = '#';
update callrecordmaster_tbr set RoutingPrefix = substring(DestinationNumber from 1 for 2), DestinationNumber = substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 3 for 1) = '#';
update callrecordmaster_tbr set RoutingPrefix = substring(DestinationNumber from 1 for 3), DestinationNumber = substring(DestinationNumber from 5 for 20) where substring(DestinationNumber from 4 for 1) = '#';


--massages source/dest into E.164
--NANPA
update callrecordmaster_tbr set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) not in ('+', '0');
update callrecordmaster_tbr set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update callrecordmaster_tbr set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) not in ('+', '0');
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update callrecordmaster_tbr set LRN = '+' || LRN where char_length(LRN) = 11 and substring(LRN from 1 for 1) = '1';
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


--Australia
update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 5 for 20) where substring(DestinationNumber from 1 for 4) = '0011';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 2) = '61';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 2) = '61';


--India
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 2) = '91';


--generic
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '2';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '6';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '8';
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 12 and substring(DestinationNumber from 1 for 1) = '8';


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


--retail termination
update callrecordmaster_tbr set CallType = 60 where Direction = 'O' and CallType is null and coalesce(CustomerID, '') <> '' and CustomerID in (select CustomerID from customermaster where lower(CustomerType) = 'retail');


--retail origination - tollfree
update callrecordmaster_tbr set CallType = 68 where Direction = 'I' and CallType is null and substring(DestinationNumber from 1 for 5) in ('+1800', '+1855', '+1866', '+1877', '+1888') and coalesce(CustomerID, '') <> '' and CustomerID in (select CustomerID from customermaster where lower(CustomerType) = 'retail');


--retail origination
update callrecordmaster_tbr set CallType = 65 where Direction = 'I' and CallType is null and substring(DestinationNumber from 1 for 5) not in ('+1800', '+1855', '+1866', '+1877', '+1888') and coalesce(CustomerID, '') <> '' and CustomerID in (select CustomerID from customermaster where lower(CustomerType) = 'retail');


--tollfree term
update callrecordmaster_tbr set CallType = 40 where Direction = 'O' and CallType is null and substring(DestinationNumber from 1 for 5) in ('+1800', '+1855', '+1866', '+1877', '+1888');



RAISE NOTICE 'Flagging international calls.'; gentime = TIMEOFDAY();
--add code here to assign call type 25 for international NANPA outbound destinations
update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 5) in ('+1242', '+1246', '+1264', '+1268', '+1284', '+1340', '+1345', '+1441', '+1473', '+1649', '+1664', '+1758', '+1767', '+1784', '+1787', '+1809', '+1829', '+1849', '+1868', '+1869', '+1876');

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '00';

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+2' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+3' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+4' and char_length(destinationnumber) = 11;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+4' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+4' and char_length(destinationnumber) = 13;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+4' and char_length(destinationnumber) = 14;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+5' and char_length(destinationnumber) = 11;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+5' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+5' and char_length(destinationnumber) = 13;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+5' and char_length(destinationnumber) = 14;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+5' and char_length(destinationnumber) = 15;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+6' and char_length(destinationnumber) = 11;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+6' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+6' and char_length(destinationnumber) = 13;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+8' and char_length(destinationnumber) = 11;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+8' and char_length(destinationnumber) = 12;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+8' and char_length(destinationnumber) = 13;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+9' and char_length(destinationnumber) = 11;

update callrecordmaster_tbr set CallType = 25 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+9' and char_length(destinationnumber) = 13;


/*south africa and other*/
update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 3 for 10) where CallType = 25
    and substring(DestinationNumber from 1 for 2) = '00' and char_length(DestinationNumber) = 12;

update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 3 for 11) where CallType = 25
    and substring(DestinationNumber from 1 for 2) = '00' and char_length(DestinationNumber) = 13;

update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 3 for 12) where CallType = 25
    and substring(DestinationNumber from 1 for 2) = '00' and char_length(DestinationNumber) = 14;
    

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);





--toll-free orig
update callrecordmaster_tbr set CallType = 30 where Direction = 'I' and CallType is null 
    and substring(DestinationNumber from 1 for 5) = '+1877' and coalesce(CustomerID, '') <> '';

update callrecordmaster_tbr set CallType = 30 where Direction = 'I' and CallType is null 
    and substring(DestinationNumber from 1 for 5) = '+1800' and coalesce(CustomerID, '') <> '';

update callrecordmaster_tbr set CallType = 30 where Direction = 'I' and CallType is null 
    and substring(DestinationNumber from 1 for 5) = '+1855' and coalesce(CustomerID, '') <> '';

update callrecordmaster_tbr set CallType = 30 where Direction = 'I' and CallType is null 
    and substring(DestinationNumber from 1 for 5) = '+1866' and coalesce(CustomerID, '') <> '';

update callrecordmaster_tbr set CallType = 30 where Direction = 'I' and CallType is null 
    and substring(DestinationNumber from 1 for 5) = '+1888' and coalesce(CustomerID, '') <> '';


--tiered orig
update callrecordmaster_tbr set CallType = 15 where Direction = 'I' and CallType is null 
    and coalesce(CustomerID, '') <> '' and customerid in (select customerid from customermaster where lower(coalesce(customertype, 'wholesale')) = 'wholesale');

update callrecordmaster_tbr set RateCenter = 'INTERNAL' where CallType = 15 and char_length(DestinationNumber) = 4 and DestinationNumber in (select DID from didmaster);
update callrecordmaster_tbr set RateCenter = 'DEFAULT' where CallType = 15 and coalesce(RateCenter, '') = '';



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


/*south africa and other*/
update callrecordmaster_tbr set CallType = 35 where CallType is null and Direction = 'O' and char_length(DestinationNumber) in (9, 10, 11) and substring(DestinationNumber from 1 for 1) = '0';


--any NANPA calls that were not international can go to simple term.
update callrecordmaster_tbr set CallType = 35 where Direction = 'O' and CallType is null 
    and substring(DestinationNumber from 1 for 2) = '+1' and char_length(destinationnumber) = 12 and customerid in (select customerid from customermaster where lower(coalesce(customertype, 'wholesale')) = 'wholesale');


EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;