CREATE OR REPLACE FUNCTION "fnCalculateConcurrentCalls"()
  RETURNS integer AS
$BODY$


DECLARE 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CCDateTime timestamp without time zone; 
MaxCCDateTime timestamp without time zone; 
gentime timestamp;
iConcurrentCalls int;
iSamplingInterval int = 60; --(seconds). The lower the interval the more accurate it will be but slower as well.
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
RAISE NOTICE 'Interval (seconds): %', iSamplingInterval;

-- get datetime of the last calc we did.
SELECT INTO CCDateTime max(CallDateTime) FROM concurrentcalls;
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

	select into iConcurrentCalls count(*) from callrecordmaster where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second';

	insert into concurrentcalls (CallDateTime, ConcurrentCalls)
	values  (CCDateTime, iConcurrentCalls);

	CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';

END LOOP;

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();


RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnCalculateConcurrentCalls', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), null);

RETURN 00000;

END;



$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
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


CREATE OR REPLACE FUNCTION "fnCalculateConcurrentCallsDirectionRateCenter"()
  RETURNS integer AS
$BODY$


DECLARE 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CCDateTime timestamp without time zone; 
MaxCCDateTime timestamp without time zone; 
iConcurrentCallsIN int;
iConcurrentCallsOUT int;
iSamplingInterval int = 60; --(seconds). The lower the interval the more accurate it will be but slower as well.
id varchar(50);
curs2 refcursor;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
RAISE NOTICE 'Interval (seconds): %', iSamplingInterval;

-- get datetime of the last calc we did.
SELECT INTO CCDateTime max(CallDateTime) FROM "ConcurrentCallsDirectionRateCenter";

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

	OPEN curs2 FOR SELECT distinct (coalesce(RateCenter, '')) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second';

	LOOP
		FETCH curs2 INTO id;
		EXIT WHEN NOT FOUND;
	
		select into iConcurrentCallsIN count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'I' and coalesce(RateCenter, '') = id;

		insert into "ConcurrentCallsDirectionRateCenter" (CallDateTime, Direction, RateCenter, ConcurrentCalls)
	                                         values  (CCDateTime, 'I', id, iConcurrentCallsIN);


		select into iConcurrentCallsOUT count(*) from "CallRecordMaster" where CCDateTime between CallDateTime and CallDateTime + Duration * interval '1 second' and Direction = 'O' and coalesce(RateCenter, '') = id;

		insert into "ConcurrentCallsDirectionRateCenter" (CallDateTime, Direction, RateCenter, ConcurrentCalls)
	                                          values  (CCDateTime, 'O', id, iConcurrentCallsOUT);
	
	
		
	END LOOP;
	close curs2;
	
	CCDateTime := CCDateTime + iSamplingInterval * interval '1 second';

END LOOP;


-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();


RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO "ProcessHistory" VALUES('fnCalculateConcurrentCallsDirectionRateCenter',StartDateTime,EndDateTime,age(EndDateTime,StartDateTime),null);

RETURN 00000;

END;



$BODY$
  LANGUAGE plpgsql;--======================================================================--
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


--massages source/dest into E.164
update callrecordmaster_tbr set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update callrecordmaster_tbr set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update callrecordmaster_tbr set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) not in ('+', '0');
update callrecordmaster_tbr set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update callrecordmaster_tbr set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';


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

$$ LANGUAGE plpgsql;--======================================================================--
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

CREATE OR REPLACE FUNCTION "fnDeleteBillingBatch"(IN billingbatchid_in "varchar") RETURNS int AS $$


DECLARE 


BEGIN

DELETE FROM billingbatchdetails WHERE billingbatchid = billingbatchid_in;

DELETE FROM billingbatchmaster WHERE billingbatchid = billingbatchid_in;

update onetimechargequeue set billingbatchid = null where billingbatchid = billingbatchid_in;

UPDATE callrecordmaster SET billingbatchid = null WHERE billingbatchid = billingbatchid_in;

RETURN 00000;

END;


$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION "fnGenerateBillingBatch"(IN billingbatchid_in "varchar", IN billingdate_in "date", IN duedate_in "date", IN billingcycleid_in "varchar", IN usageperiodend_in "date", IN recurringfeeperiodstart_in "date", IN recurringfeeperiodend_in "date") RETURNS int AS $$



DECLARE 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
gentime timestamp;
curs1 CURSOR FOR SELECT DISTINCT (CustomerID) FROM customermaster WHERE customermaster.BillingCycle = billingcycleid_in ORDER BY CustomerID;
id varchar(15);

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- Verify at least one customer is in the cycle
RAISE NOTICE 'Verifying at least one customer is in the cycle.';
gentime = TIMEOFDAY();
IF EXISTS (SELECT * FROM customermaster WHERE customermaster.BillingCycle = billingcycleid_in) THEN
	RAISE NOTICE 'Billing Cycle Found';
ELSE
	RAISE NOTICE 'ERROR: Billing Cycle Not Found';
	RAISE EXCEPTION 'ERROR: Billing Cycle Not Found';
	RAISE NOTICE 'Function Exiting.';
	RETURN 1;
END IF;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- Verify the BillingBatchID has not already been used
RAISE NOTICE 'Verifying the BillingBatchID has not already been used.';
gentime = TIMEOFDAY();
IF EXISTS (SELECT * FROM billingbatchmaster WHERE billingbatchmaster.BillingBatchID = billingbatchid_in) THEN
	RAISE NOTICE 'ERROR: BillingBatchID already used';
	RAISE EXCEPTION 'ERROR: BillingBatchID already used';
	RAISE NOTICE 'Function Exiting.';
	RETURN 1;
ELSE
	RAISE NOTICE 'Unique BillingBatchID.';
END IF;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- Add entry to BillingBatchMaster
RAISE NOTICE 'Adding entry to BillingBatchMaster.';
gentime = TIMEOFDAY();
INSERT INTO billingbatchmaster (billingbatchid, billingdate, duedate, billingcycleid, usageperiodend) 
                        VALUES (billingbatchid_in, billingdate_in, duedate_in, billingcycleid_in, usageperiodend_in);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




-- Update CallRecordMaster
RAISE NOTICE 'Updating CallRecordMaster.'; gentime = TIMEOFDAY();
UPDATE callrecordmaster SET BillingBatchID = billingbatchid_in WHERE callrecordmaster.BillingBatchID IS NULL AND CAST(callrecordmaster.CallDateTime AS DATE) <= UsagePeriodEnd_in AND callrecordmaster.CustomerID IN (SELECT callrecordmaster.CustomerID FROM customermaster WHERE customermaster.BillingCycle = BillingCycleID_in);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- Create usage / LRN / CNAM entries in BillingBatchDetails
RAISE NOTICE 'Creating entries in BillingBatchDetails.'; gentime = TIMEOFDAY();
OPEN curs1;

LOOP
	FETCH curs1 INTO id;
	EXIT WHEN NOT FOUND;

	--add recurring charges
	--RAISE NOTICE 'Adding recurring charges.';
	--gentime = TIMEOFDAY();
	INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
	                          SELECT billingbatchid_in, id, null, 5, ChargeDesc, UnitAmount * Quantity, Quantity, recurringfeeperiodstart_in, recurringfeeperiodend_in from recurringchargemaster where CustomerID = id and ActivationDate <= recurringfeeperiodstart_in order by ActivationDate, RecurringChargeID;
		
	--EndDateTime = TIMEOFDAY();
	--RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


	--add one-time charges
	update onetimechargequeue set BillingBatchID = billingbatchid_in where CustomerID = id and coalesce(BillingBatchID, '') = '';
	
	INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
	                          SELECT billingbatchid_in, id, null, 35, ChargeDesc, UnitAmount * Quantity, Quantity, null, null from onetimechargequeue where CustomerID = id and BillingBatchID = billingbatchid_in order by ChargeDate, OneTimeChargeID;
	


	-- Create entry for Interstate
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 10) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 10, 10, 'Interstate Calls', sum(crm.retailprice - crm.lrndipfee - crm.cnamfee), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 10 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for inter
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 10, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 10 ;
		
	END IF;	

	
	-- Create entry for Intrastate
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 5) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 5, 10, 'Intrastate Calls', sum(crm.retailprice - crm.lrndipfee - crm.cnamfee), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 5 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for intra
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 5, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 5 ;

		
	END IF;

	-- Create entry for Indeterminate Jurisdiction 
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 20) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 20, 10, 'Indeterminate Jurisdiction Calls', sum(crm.retailprice - crm.lrndipfee - crm.cnamfee), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 20 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for indeterminate Jurisdiction
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 20, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 20 ;

	
	END IF;

	-- Create entry for International
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 25) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 25, 10, 'International Calls', sum(crm.retailprice), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 25 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for International
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 25, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 25 ;

	
	END IF;

	-- Create entry for Simple Termination
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 35) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 35, 10, 'Simple Termination Calls', sum(crm.retailprice), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 35 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for simple term
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 35, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 35 ;

	
	END IF;
	
	-- Create entry for Toll Free
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 30) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 30, 10, 'Toll Free Calls', sum(crm.retailprice), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 30 group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for toll free
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 30, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 30 ;

	
	END IF;

	-- Create entry for each tier of tiered orig
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 15) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 15, 10, concat('Tiered Origination Calls - Tier ', BilledTier), sum(crm.retailprice), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in and CustomerID = id and CallType = 15 group by BilledTier, date_part('month', CallDateTime) order by date_part('month', CallDateTime);

		--create taxes for tiered orig
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		select billingbatchid_in, id, 15, 40, taxes.TaxType, bills.LineItemAmount * taxes.TaxRate, 0, bills.periodstartdate, bills.periodenddate from billingbatchdetails as bills inner join customertaxsetup as taxes on bills.CustomerID = taxes.CustomerID and bills.CallType = taxes.CallType where bills.billingbatchid = billingbatchid_in and bills.customerid = id and bills.calltype = 15 ;

	
	END IF;

	-- Create entry for CNAM Charges
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in AND CustomerID = id AND cnamfee > 0) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 0, 20, 'CNAM Charges', sum(crm.cnamfee), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in AND CustomerID = id group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);
	
	END IF;

	-- Create entry for LRN DIP Charges
	IF EXISTS (SELECT * FROM callrecordmaster WHERE BillingBatchID = billingbatchid_in AND CustomerID = id AND lrndipfee > 0) THEN
		INSERT INTO billingbatchdetails (billingbatchid, customerid, calltype, LineItemType, lineitemdesc, lineitemamount, lineitemquantity, periodstartdate, periodenddate) 
		SELECT billingbatchid_in, id, 0, 30, 'LRN Dip Charges', sum(crm.lrndipfee), count(*), min(CallDateTime), max(CallDateTime) from callrecordmaster as crm where BillingBatchID = billingbatchid_in AND CustomerID = id group by date_part('month', CallDateTime) order by date_part('month', CallDateTime);
	
	END IF;
	
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnGenerateBillingBatch', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), 0);

RETURN 00000;

END;



$$ LANGUAGE plpgsql;

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

$$ LANGUAGE plpgsql;--======================================================================--
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

$$ LANGUAGE plpgsql;--======================================================================--
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

$$ LANGUAGE plpgsql;--======================================================================--
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

CREATE OR REPLACE FUNCTION "fnMoveHELDCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_held (
        CallID varchar(100)     PRIMARY KEY
)ON COMMIT DROP;

-- moving the oldest "callid" from HELD into that table. Edit PROCESSING_LIMIT to change how many.
INSERT INTO tmp_held (CallID) SELECT CallID FROM callrecordmaster_held ORDER BY CallDateTime LIMIT PROCESSING_LIMIT;

INSERT INTO callrecordmaster_tbr (callid,customerid,calltype,calldatetime,duration,direction,sourceip,originatingnumber,destinationnumber,lrn,cnamdipped,ratecenter, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, ratecenter, CarrierID, wholesalerate, wholesaleprice FROM callrecordmaster_held WHERE callid IN (SELECT callid FROM tmp_held);

DELETE FROM callrecordmaster_held WHERE CallID IN (SELECT callid FROM tmp_held);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;--======================================================================--
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

$$ LANGUAGE plpgsql;--======================================================================--
/*  OpenCDRRate Rate your call records.
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

$$ LANGUAGE plpgsql;--======================================================================--
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

CREATE OR REPLACE FUNCTION "fnRateIndeterminateJurisdictionCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime TIMESTAMP;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 20
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 20;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrindjur (
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
				BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN      varchar(50),
		LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
		BilledNumber varchar(50), 
		NPANXXX varchar(7),
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
        CNAMDipped boolean,
		CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
		WholesaleRate numeric (19, 7),
		WholesalePrice numeric(19, 7),
		RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrindjur (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, Destinationnumber, LRN, LRNDipFee, BilledNumber, NPANXXX, RetailRate, RetailPrice, CNAMDipped, CNAMFee, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, 0, null, null, null, null, CNAMdipped, 0, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 20
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Creating index cdrindjur_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrindjur_rawduration ON cdrindjur (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrindjur WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrindjur SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrindjur SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrindjur SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrindjur SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET NPANXXX = SUBSTRING(cdrindjur.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


RAISE NOTICE 'Creating index CustomerID_NPANXXX.'; gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrindjur(CustomerID,NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


RAISE NOTICE 'Creating index LRN_CustomerID.'; gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrindjur(LRN,CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET CNAMFee = CNAMDipRate FROM customermaster WHERE cdrindjur.CNAMDipped = true AND cdrindjur.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET LRNDipFee = LRNDipRate FROM customermaster 
WHERE trim(both ' ' from LRN) <> '' AND 
LRN is not null AND 
cdrindjur.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

RAISE NOTICE 'Regenerating effective sheet'; gentime = TIMEOFDAY();
-- Regenerate intra effective sheet if any customers in this batch have 5 as their IndeterminateJurisdictionCallType
IF EXISTS(SELECT * from cdrindjur where CustomerID in (SELECT CustomerID FROM customermaster WHERE IndeterminateJurisdictionCallType = 5)) THEN

	IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') = 0
THEN
		TRUNCATE TABLE effectiveintrastateratemaster;

		gentime = TIMEOFDAY();
		RAISE NOTICE 'Generating new intra effective rates. This may take several minutes. Process Started At %', gentime;

		INSERT INTO effectiveintrastateratemaster 
		SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM intrastateratemaster AS ratesheet
		INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM intrastateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
		ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect.EffectiveDate;
		GET DIAGNOSTICS rec_count = ROW_COUNT;

		DELETE FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE';
		INSERT INTO systemsettings_date VALUES ('INTRA_EFFECTIVE_RATE_DATE', CDRDate);
		EndDateTime = timeofday();
		RAISE NOTICE 'Generation of new rates completed at %', EndDateTime;
		INSERT INTO processhistory VALUES('Generating Intra Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
	END IF;
end if;

-- Regenerate inter effective sheet if any customers have 10 as their IndeterminateJurisdictionCallType
IF EXISTS(SELECT * from cdrindjur where CustomerID in (SELECT CustomerID FROM customermaster WHERE IndeterminateJurisdictionCallType =10)) THEN

	IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
	WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') = 0
	THEN
			TRUNCATE TABLE effectiveinterstateratemaster;

			gentime = TIMEOFDAY();
			RAISE NOTICE 'Generating new inter effective rates. This may take several minutes. Process Started At %', gentime;

			INSERT INTO effectiveinterstateratemaster 
			SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM interstateratemaster AS ratesheet
			INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM interstateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
			ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect.EffectiveDate;
			GET DIAGNOSTICS rec_count = ROW_COUNT;

			DELETE FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE';
			INSERT INTO systemsettings_date VALUES ('INTER_EFFECTIVE_RATE_DATE', CDRDate);
			EndDateTime = timeofday();
			RAISE NOTICE 'Generation of new rates completed at %', EndDateTime;
			INSERT INTO processhistory VALUES('Generating Inter Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
	END IF;
end IF;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);
		-- get retail rates
		-- INTRA
		RAISE NOTICE 'Getting intra retail rates.'; gentime = TIMEOFDAY();
		UPDATE cdrindjur SET retailrate = effectiveintrastateratemaster.retailrate 
		FROM effectiveintrastateratemaster
		WHERE cdrindjur.customerid = effectiveintrastateratemaster.customerid AND cdrindjur.NPANXXX = effectiveintrastateratemaster.npanxxx AND cdrindjur.CustomerID in 	(select CustomerID from customermaster WHERE IndeterminateJurisdictionCallType = 5);
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);	

		-- INTER
				RAISE NOTICE 'Getting inter retail rates.'; gentime = TIMEOFDAY();
		UPDATE cdrindjur SET retailrate = effectiveinterstateratemaster.retailrate 
		FROM effectiveinterstateratemaster
		WHERE cdrindjur.customerid = effectiveinterstateratemaster.customerid AND cdrindjur.NPANXXX = effectiveinterstateratemaster.npanxxx AND cdrindjur.CustomerID in 	(select CustomerID from customermaster WHERE IndeterminateJurisdictionCallType =10);
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

		-- move all records to HELD that we could not find the rate for
		RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
		INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
		SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, 'No Rate for NPANXXX'
		FROM cdrindjur where cdrindjur.RetailRate is null AND cdrindjur.CallID not in (SELECT CallID FROM callrecordmaster_held);

		DELETE FROM callrecordmaster_tbr WHERE callrecordmaster_tbr.CallID in (SELECT CallID FROM cdrindjur WHERE RetailRate is null);

		DELETE FROM cdrindjur WHERE RetailRate is null;
		EndDateTime = TIMEOFDAY();
		RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailPrice + CNAMFee; 
-- WHERE cdrindjur.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrindjur SET RetailPrice = RetailPrice + LRNDipFee; 
-- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice
FROM cdrindjur; 
-- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrindjur);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnRateIndeterminateJurisdictionCDR', StartDateTime, EndDateTime,age(EndDateTime, StartDateTime), rec_count);




RETURN 00000;

END;





$$ LANGUAGE plpgsql;
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

CREATE OR REPLACE FUNCTION "fnRateInternationalCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for Inter call type = 25
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 25;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective international Rate Sheet
TRUNCATE TABLE effectiveinternationalratemaster;

INSERT INTO effectiveinternationalratemaster 
SELECT ratesheet.CustomerID, ratesheet.BilledPrefix, RetailRate FROM internationalratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM internationalratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, BilledPrefix) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTERNATIONAL_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTERNATIONAL_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime,gentime);

INSERT INTO processhistory VALUES('Generating International Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrinternat (
		CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15),
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
		BilledPrefix varchar(10), 
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrinternat (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, retailrate, retailprice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 25
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrinternat.sourceip = ipaddressmaster.ipaddress and coalesce(cdrinternat.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrinternat where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat where coalesce(customerid, '') = '');

DELETE FROM cdrinternat where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrinternat_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrinternat_rawduration ON cdrinternat (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN


RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
DELETE FROM cdrinternat WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrinternat SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrinternat SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--attempt to massage DestinationNumber to E.164 format.
RAISE NOTICE 'Attempt to massage to E.164 format.'; gentime = TIMEOFDAY();
update cdrinternat set destinationnumber = '+' || destinationnumber where substring(destinationnumber from 1 for 1) = '1' and char_length(destinationnumber) = 11;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
	UPDATE cdrinternat SET billedprefix = effectiveinternationalratemaster.billedprefix, retailrate = effectiveinternationalratemaster.retailrate 
	FROM effectiveinternationalratemaster
	WHERE cdrinternat.customerid = effectiveinternationalratemaster.customerid and SUBSTRING(cdrinternat.destinationnumber FROM 1 FOR i) = effectiveinternationalratemaster.billedprefix and cdrinternat.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET retailrate = effectiveinternationalratemaster.retailrate, billedprefix = '*' 
FROM effectiveinternationalratemaster
WHERE  cdrinternat.customerid = effectiveinternationalratemaster.customerid AND effectiveinternationalratemaster.billedprefix = '*' AND
cdrinternat.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, 'No rate found.'
FROM cdrinternat where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat WHERE RetailRate is null);

DELETE FROM cdrinternat WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrinternat SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, wholesalerate, wholesaleprice
FROM cdrinternat; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinternat);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnRateInternationalCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION "fnRateInterstateCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 10
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 10;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective Term Rate Sheet
TRUNCATE TABLE effectiveinterstateratemaster;

INSERT INTO effectiveinterstateratemaster 
SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM interstateratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM interstateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTER_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTER_EFFECTIVE_RATE_DATE', CDRDate);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generated % new rates. Completed in: %', rec_count, age(EndDateTime, gentime);

INSERT INTO processhistory VALUES('Interstate Rates Generated', gentime, EndDateTime, age(EndDateTime, gentime), rec_count);

END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrinter (
	CallID varchar(100)     PRIMARY KEY,
	CustomerID varchar(15),
	CallType smallint,
	CallDateTime timestamp NOT NULL,
	Duration integer NOT NULL,
	BilledDuration integer,
	Direction char(1),
	SourceIP varchar(15),
	OriginatingNumber varchar(50) NOT NULL,
	DestinationNumber varchar(50) NOT NULL,
	BilledPrefix varchar(10),
	LRN      varchar(50),
	LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
	BilledNumber varchar(50), 
	NPANXXX char(7),
	NPANXX char(6),
	RetailRate numeric(9,7),
	RetailPrice numeric(19,7),
	CNAMDipped boolean,
	CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
	RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrinter (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, lrn, lrndipfee, billednumber, npanxxx, npanxx, retailrate, retailprice, cnamdipped, cnamfee, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, LRN, 0, null, null, null, null, null, CNAMdipped, 0, wholesalerate, wholesaleprice
FROM callRecordMaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 10
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrinter.sourceip = ipaddressmaster.ipaddress and cdrinter.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);



-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrinter where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter where coalesce(customerid, '') = '');

DELETE FROM cdrinter where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrinter_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrinter_rawduration ON cdrinter (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrinter WHERE duration <= 0;
DELETE FROM callrecordmaster_tbr WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrinter SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrinter SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--massage source/dest into E.164 if possible
update cdrinter set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update cdrinter set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update cdrinter set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) <> '+';
update cdrinter set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update cdrinter set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';
update cdrinter set LRN = '+1' || lrn where char_length(LRN) = 10 and substring(LRN from 1 for 1) <> '+';
update cdrinter set LRN = '+' || lrn where char_length(LRN) = 11 and substring(LRN from 1 for 1) = '1';
update cdrinter set LRN = '+' || substring(LRN from 4 for 20) where substring(LRN from 1 for 3) = '011';



-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrinter SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrinter SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- filter out BilledNumbers with invalid format 
RAISE NOTICE 'filter out BilledNumbers with invalid fomat to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, 'Invalid BilledNumber Format. Must be E.164.'
FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1';

DELETE FROM callrecordmaster_tbr WHERE callid IN (SELECT CallID FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1');

DELETE FROM cdrinter WHERE SUBSTRING(cdrinter.BilledNumber from 1 for 2) <> '+1';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'Creating index CustomerID_NPANXXX.'; gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrinter(CustomerID, NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'Creating index LRN_CustomerID.'; gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrinter(LRN, CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET NPANXXX = SUBSTRING(cdrinter.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- parse the NPANXX out of the billed number
RAISE NOTICE 'Parsing NPANXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET NPANXX = SUBSTRING(cdrinter.billednumber FROM 3 FOR 6);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx
FROM effectiveinterstateratemaster
WHERE cdrinter.customerid = effectiveinterstateratemaster.customerid AND cdrinter.NPANXXX = effectiveinterstateratemaster.npanxxx;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXX.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx 
FROM effectiveinterstateratemaster
WHERE cdrinter.customerid = effectiveinterstateratemaster.customerid AND cdrinter.NPANXX = effectiveinterstateratemaster.npanxxx and cdrinter.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET retailrate = effectiveinterstateratemaster.retailrate, BilledPrefix = effectiveinterstateratemaster.npanxxx 
FROM effectiveinterstateratemaster
WHERE  cdrinter.customerid = effectiveinterstateratemaster.customerid AND effectiveinterstateratemaster.npanxxx = '*' AND
cdrinter.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, 'No rate for NPANXX or NPANXXX.'
FROM cdrinter where RetailRate is null; --AND CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'deleting moved records from temp table.';
gentime = TIMEOFDAY();
DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter WHERE RetailRate is null);

DELETE FROM cdrinter WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET CNAMFee = CNAMDipRate FROM customermaster WHERE cdrinter.CNAMDipped = true AND cdrinter.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET LRNDipFee = LRNDipRate FROM customermaster
WHERE trim(both ' ' from LRN) <> '' AND 
not LRN is null AND 
cdrinter.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailPrice + CNAMFee; -- WHERE cdrinter.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.'; gentime = TIMEOFDAY();
UPDATE cdrinter SET RetailPrice = RetailPrice + LRNDipFee; -- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, wholesalerate, wholesaleprice
FROM cdrinter; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrinter);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

END;

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnrateinterstatecdr', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

RETURN 00000;

END;



$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION "fnRateIntrastateCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for intra call type = 5
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 5;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective Term Rate Sheet
TRUNCATE TABLE effectiveintrastateratemaster;

INSERT INTO effectiveintrastateratemaster 
SELECT ratesheet.CustomerID, ratesheet.NPANXXX, RetailRate FROM intrastateratemaster AS ratesheet
INNER JOIN (SELECT CustomerID, NPANXXX, MAX(effectivedate) AS effectivedate FROM intrastateratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, NPANXXX) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.NPANXXX = inaffect.NPANXXX AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM systemsettings_date WHERE SettingName = 'INTRA_EFFECTIVE_RATE_DATE';
INSERT INTO systemsettings_date VALUES ('INTRA_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed in %', age(EndDateTime,gentime);

INSERT INTO processhistory VALUES('Generating Intra Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrintra (
	CallID varchar(100)     PRIMARY KEY,
	CustomerID varchar(15),
	CallType smallint,
	CallDateTime timestamp NOT NULL,
	Duration integer NOT NULL,
	BilledDuration integer,
	Direction char(1),
	SourceIP varchar(15),
	OriginatingNumber varchar(50) NOT NULL,
	DestinationNumber varchar(50) NOT NULL,
	BilledPrefix varchar(10),
	LRN      varchar(50),
	LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
	BilledNumber varchar(50), 
	NPANXXX char(7),
	NPANXX char(6),
	RetailRate numeric(9,7),
	RetailPrice numeric(19,7),
	CNAMDipped boolean,
	CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
	CarrierID varchar(100),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
	RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.';
gentime = TIMEOFDAY();

INSERT INTO cdrintra (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, lrn, lrndipfee, billednumber, npanxxx, npanxx, retailrate, retailprice, cnamdipped, cnamfee, CarrierID, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, LRN, 0, null, null, null, null, null, CNAMdipped, 0, CarrierID, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 5
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
EndDateTime = TIMEOFDAY();
RAISE NOTICE '% new records to be rated. Gathered in: %', rec_count, age(EndDateTime,gentime) ;
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrintra SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrintra.sourceip = ipaddressmaster.ipaddress and cdrintra.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrintra where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra where coalesce(customerid, '') = '');

DELETE FROM cdrintra where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);




-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrintra_rawduration.';
gentime = TIMEOFDAY();
CREATE INDEX cdrintra_rawduration ON cdrintra (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN

-- delete 0 duration
RAISE NOTICE 'Deleting 0 duration.';
gentime = TIMEOFDAY();
DELETE FROM cdrintra WHERE duration <= 0;
DELETE FROM callrecordmaster_tbr WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrintra SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrintra SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--massage source/dest into E.164 if possible
update cdrintra set OriginatingNumber = '+1' || OriginatingNumber where char_length(OriginatingNumber) = 10 and substring(OriginatingNumber from 1 for 1) <> '+';
update cdrintra set OriginatingNumber = '+' || OriginatingNumber where char_length(OriginatingNumber) = 11 and substring(OriginatingNumber from 1 for 1) = '1';
update cdrintra set DestinationNumber = '+1' || DestinationNumber where char_length(DestinationNumber) = 10 and substring(DestinationNumber from 1 for 1) <> '+';
update cdrintra set DestinationNumber = '+' || DestinationNumber where char_length(DestinationNumber) = 11 and substring(DestinationNumber from 1 for 1) = '1';
update cdrintra set DestinationNumber = '+' || substring(DestinationNumber from 4 for 20) where substring(DestinationNumber from 1 for 3) = '011';
update cdrintra set LRN = '+1' || lrn where char_length(LRN) = 10 and substring(LRN from 1 for 1) <> '+';
update cdrintra set LRN = '+' || lrn where char_length(LRN) = 11 and substring(LRN from 1 for 1) = '1';
update cdrintra set LRN = '+' || substring(LRN from 4 for 20) where substring(LRN from 1 for 3) = '011';


-- bill from LRN if available, use DestinationNumber if it is not.
RAISE NOTICE 'Billing from LRN if available.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET BilledNumber = DestinationNumber WHERE LRN is null;
UPDATE cdrintra SET BilledNumber = DestinationNumber WHERE trim(both ' ' from LRN) = '';
UPDATE cdrintra SET BilledNumber = LRN WHERE BilledNumber is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- filter out BilledNumbers with invalid format 
RAISE NOTICE 'filter out BilledNumbers with invalid format to HELD table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, 'Invalid BilledNumber Format. Must be E.164.'
FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1';

DELETE FROM callrecordmaster_tbr WHERE callid IN (SELECT CallID FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1');

DELETE FROM cdrintra WHERE SUBSTRING(cdrintra.BilledNumber from 1 for 2) <> '+1';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXXX out of the billed number
RAISE NOTICE 'Parsing NPANXXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET NPANXXX = SUBSTRING(cdrintra.billednumber FROM 3 FOR 7);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- parse the NPANXX out of the billed number
RAISE NOTICE 'Parsing NPANXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET NPANXX = SUBSTRING(cdrintra.billednumber FROM 3 FOR 6);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




RAISE NOTICE 'Creating index CustomerID_NPANXXX.';
gentime = TIMEOFDAY();
CREATE INDEX CustomerID_NPANXXX ON cdrintra(CustomerID, NPANXXX);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


RAISE NOTICE 'Creating index LRN_CustomerID.';
gentime = TIMEOFDAY();
CREATE INDEX LRN_CustomerID ON cdrintra(LRN, CustomerID);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

--RAISE NOTICE 'Analyzing temporary table. %', TIMEOFDAY();
--ANALYZE cdrintra;


-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx 
FROM effectiveintrastateratemaster
WHERE cdrintra.customerid = effectiveintrastateratemaster.customerid AND cdrintra.NPANXXX = effectiveintrastateratemaster.npanxxx;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- get retail rates
RAISE NOTICE 'Getting retail rates on NPANXX.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx 
FROM effectiveintrastateratemaster
WHERE cdrintra.customerid = effectiveintrastateratemaster.customerid AND cdrintra.NPANXX = effectiveintrastateratemaster.npanxxx and cdrintra.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- get default retail rates
RAISE NOTICE 'Getting default rates.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET retailrate = effectiveintrastateratemaster.retailrate, BilledPrefix = effectiveintrastateratemaster.npanxxx  
FROM effectiveintrastateratemaster
WHERE  cdrintra.customerid = effectiveintrastateratemaster.customerid AND effectiveintrastateratemaster.npanxxx = '*' AND
cdrintra.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, 'No rate for NPANXX or NPANXXX.'
FROM cdrintra where RetailRate is null; -- AND CallID not in (SELECT CallID FROM "CallRecordMaster_HELD");
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

RAISE NOTICE 'deleting moved records from temp table.';
gentime = TIMEOFDAY();
DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra WHERE RetailRate is null);

DELETE FROM cdrintra WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- retrieve CNAMRate from CustomerMaster 
RAISE NOTICE 'Retrieving CNAMRate.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET CNAMFee = CNAMDipRate FROM customermaster WHERE cdrintra.CNAMDipped = true AND cdrintra.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- retrieve LRNDipFee from CustomerMaster
RAISE NOTICE 'Retrieving LRNDIPFee.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET LRNDipFee = LRNDipRate FROM customermaster 
WHERE trim(both ' ' from LRN) <> '' AND 
not LRN is null AND 
cdrintra.CustomerID = customermaster.CustomerID;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- calculate retail price
RAISE NOTICE 'Calculating retail price.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- add CNAMFee to applicable calls
RAISE NOTICE 'Adding CNAMFee to applicable calls.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailPrice + CNAMFee; -- WHERE cdrintra.CNAMDipped = true;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


--add LRNDipFee to applicable calls
RAISE NOTICE 'Adding LRNDipFee to applicable calls.';
gentime = TIMEOFDAY();
UPDATE cdrintra SET RetailPrice = RetailPrice + LRNDipFee; -- WHERE trim(both ' ' from LRN) <> '' AND LRN is not null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.';
gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix,current_timestamp, RetailRate, CNAMdipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice
FROM cdrintra; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrintra);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime, StartDateTime);

INSERT INTO processhistory VALUES('fnrateintrastatecdr', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;



$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION "fnRateSimpleTerminationCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for Inter call type = 35
SELECT INTO CDRDate MIN(CallDateTime) FROM callRecordMaster_tbr
WHERE CallType = 35;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
--IF CDRDate <> (SELECT SettingValue FROM systemsettings_date
--WHERE SettingName = 'SIMPLETERM_EFFECTIVE_RATE_DATE') OR (select count(*) FROM systemsettings_date WHERE SettingName = 'SIMPLETERM_EFFECTIVE_RATE_DATE') = 0
--THEN
	gentime = TIMEOFDAY();
	RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
	-- Generate Effective SIMPLETERM Rate Sheet
	TRUNCATE TABLE effectivesimpleterminationratemaster;

	INSERT INTO effectivesimpleterminationratemaster 
	SELECT ratesheet.CustomerID, ratesheet.BilledPrefix, RetailRate FROM simpleterminationratemaster AS ratesheet
	INNER JOIN (SELECT CustomerID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM simpleterminationratemaster WHERE effectivedate <= CDRDate GROUP BY CustomerID, BilledPrefix) AS inaffect
	ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
	GET DIAGNOSTICS rec_count = ROW_COUNT;

	DELETE FROM systemsettings_date WHERE SettingName = 'SIMPLETERM_EFFECTIVE_RATE_DATE';
	INSERT INTO systemsettings_date VALUES ('SIMPLETERM_EFFECTIVE_RATE_DATE', CDRDate);

	EndDateTime = TIMEOFDAY();
	RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime,gentime);

	INSERT INTO processhistory VALUES('Generating Simple Termination Rate Sheet', gentime, EndDateTime,age(EndDateTime, gentime),rec_count);
--END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrsimterm (
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15),
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
		BilledPrefix varchar(10), 
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
		CarrierID varchar(100),
		RateCenter varchar(50),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrsimterm (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, retailrate, retailprice, CarrierID, RateCenter, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 35
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find customerid by originating number.
RAISE NOTICE 'Retrieving customerID based on Originating Number.'; gentime = TIMEOFDAY();
UPDATE cdrsimterm SET customerid = didmaster.customerid 
FROM didmaster
WHERE cdrsimterm.originatingnumber = didmaster.DID and coalesce(cdrsimterm.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrsimterm SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrsimterm.sourceip = ipaddressmaster.ipaddress and coalesce(cdrsimterm.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrsimterm where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrsimterm where coalesce(customerid, '') = '');

DELETE FROM cdrsimterm where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrsimterm_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrsimterm_rawduration ON cdrsimterm (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrsimterm WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrsimterm SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrsimterm SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrsimterm SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrsimterm SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrsimterm SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrsimterm SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrsimterm SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
	UPDATE cdrsimterm SET billedprefix = effectivesimpleterminationratemaster.billedprefix, retailrate = effectivesimpleterminationratemaster.retailrate 
	FROM effectivesimpleterminationratemaster
	WHERE cdrsimterm.customerid = effectivesimpleterminationratemaster.customerid and SUBSTRING(cdrsimterm.destinationnumber FROM 1 FOR i) = effectivesimpleterminationratemaster.billedprefix and cdrsimterm.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrsimterm SET retailrate = effectivesimpleterminationratemaster.retailrate, billedprefix = '*' 
FROM effectivesimpleterminationratemaster
WHERE  cdrsimterm.customerid = effectivesimpleterminationratemaster.customerid AND effectivesimpleterminationratemaster.billedprefix = '*' AND
cdrsimterm.retailrate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, RateCenter, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, RateCenter, wholesalerate, wholesaleprice, 'No rate found.'
FROM cdrsimterm where RetailRate is null AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrsimterm WHERE RetailRate is null);

DELETE FROM cdrsimterm WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrsimterm SET RetailPrice = RetailRate * BilledDuration / 60.00000;


-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, BilledPrefix, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'O', SourceIP, OriginatingNumber, DestinationNumber, null, 0, DestinationNumber, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, RateCenter, wholesalerate, wholesaleprice
FROM cdrsimterm; -- WHERE CallID not in (SELECT CallID FROM callrecordmaster); This can go in as a failsafe, but will slow things down

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrsimterm);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Funtion took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnRateSimpleTerminationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION "fnRateTieredOriginationCDR"() RETURNS int AS $$



DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;

BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for inter call type = 15
SELECT INTO CDRDate MIN(CallDateTime) FROM callrecordmaster_tbr
WHERE CallType = 15;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrorig (
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15),
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN      varchar(50),
		LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
		BilledNumber varchar(50), 
		NPANXXX varchar(7),
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
        CNAMDipped boolean,
		CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
		BilledTier smallint,
		RateCenter varchar(50),
		CarrierID varchar(100),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrorig (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, NPANXXX, RetailRate, RetailPrice, CNAMDipped, CNAMFee, BilledTier, RateCenter, CarrierID, wholesalerate, wholesaleprice)
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, 0, null, null, null, null, CNAMdipped, 0, null, RateCenter, CarrierID, wholesalerate, wholesaleprice
FROM callrecordmaster_tbr
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 15
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET customerid = ipaddressmaster.customerid 
FROM ipaddressmaster
WHERE cdrorig.sourceip = ipaddressmaster.ipaddress and coalesce(cdrorig.customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);


-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrorig where coalesce(customerid, '') = '' AND CallID not in (SELECT CallID FROM callrecordmaster_held);

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT CallID FROM cdrorig where coalesce(customerid, '') = '');

DELETE FROM cdrorig where coalesce(customerid, '') = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);




CREATE INDEX cdrorig_rawduration ON cdrorig (Duration);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrorig SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrorig SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- find the tier for each call based on rate center
RAISE NOTICE 'Finding the tier for each call based on rate center.'; gentime = TIMEOFDAY();
UPDATE cdrorig SET BilledTier = tieredoriginationratecentermaster.tier 
FROM tieredoriginationratecentermaster
WHERE cdrorig.ratecenter = tieredoriginationratecentermaster.ratecenter;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move all records to HELD that we could not find the tier for
RAISE NOTICE 'Moving records with no tier to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, 'Invalid Rate Center'
FROM cdrorig where BilledTier is null;

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT callid FROM cdrorig WHERE BilledTier is null);

DELETE FROM cdrorig WHERE BilledTier is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- create a de-versioned rate sheet based on call date
RAISE NOTICE 'create a de-versioned rate sheet based on call date.';
CREATE TEMPORARY TABLE origrates(
	CustomerID varchar(50) NOT NULL,
	Tier smallint NOT NULL,
	RetailRate numeric(9, 7) NOT NULL,
PRIMARY KEY (CustomerID, Tier)
) ON COMMIT DROP;

INSERT INTO origrates (CustomerID, Tier, Retailrate)
SELECT ratesheet.CustomerID, ratesheet.Tier, ratesheet.RetailRate FROM tieredoriginationratemaster AS ratesheet INNER JOIN (SELECT CustomerID, Tier, MAX(EffectiveDate) AS EffectiveDate FROM tieredoriginationratemaster WHERE EffectiveDate <= CDRDate GROUP BY CustomerID, Tier) AS inaffect ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.Tier = inaffect.Tier AND ratesheet.EffectiveDate = inaffect.EffectiveDate;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- get rate by tier
RAISE NOTICE 'Get rate by tier.';
UPDATE cdrorig SET RetailRate =  origrates.RetailRate FROM origrates WHERE cdrorig.CustomerID = origrates.CustomerID AND cdrorig.BilledTier = origrates.Tier;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- move to HELD if there is no Tier rate found
RAISE NOTICE 'Moving records with no tier rate to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster_held (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, RateCenter, CarrierID, wholesalerate, wholesaleprice, 'No Tier Rate Found'
FROM cdrorig WHERE RetailRate is null;

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT callid FROM cdrorig WHERE RetailRate is null);

DELETE FROM cdrorig WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate retail price
RAISE NOTICE 'Calculate retail price.';
UPDATE cdrorig SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the callrecordmaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO callrecordmaster (CallID, CustomerID, CallType, CallDateTime,  Duration, BilledDuration, Direction, SourceIP, OriginatingNumber,  DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, BilledTier, RateCenter, RetailPrice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime,  Duration, BilledDuration, 'I', SourceIP, OriginatingNumber,  DestinationNumber, LRN, LRNDipFee, OriginatingNumber, CURRENT_TIMESTAMP, RetailRate, CNAMDipped, CNAMFee, BilledTier, RateCenter, RetailPrice, CarrierID, wholesalerate, wholesaleprice FROM cdrorig;

DELETE FROM callrecordmaster_tbr WHERE CallID in (SELECT callid FROM cdrorig);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime, gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Function took %', age(EndDateTime,StartDateTime);

INSERT INTO processhistory VALUES('fnRateTieredOriginationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

RETURN 00000;

END;





$$ LANGUAGE plpgsql;
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

CREATE OR REPLACE FUNCTION "fnRateTollFreeOriginationCDR"() RETURNS int AS $$


DECLARE 
PROCESSING_LIMIT int = 100000;  -- Rate of how many rates will be processed at a time
rec_count int;   -- Will hold the ROW_COUNT of how many rates were processed 
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
CDRDate date;   -- The date of the calls to be rated
gentime timestamp;


BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;

-- get date of the oldest unrated (TBR) CDR for Inter call type = 30
SELECT INTO CDRDate MIN(CallDateTime) FROM "callrecordmaster_tbr"
WHERE CallType = 30;
IF CDRDATE IS NULL THEN 
RAISE NOTICE 'No calls to rate.';
RETURN 00000;
END IF;

RAISE NOTICE 'CDR Date: %', CDRDate;
RAISE NOTICE 'PROCESSING_LIMIT: %', PROCESSING_LIMIT;

-- determine if we need to regenerate "effective" rate sheet
IF CDRDate <> (SELECT SettingValue FROM "systemsettings_date"
WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE') OR (select count(*) FROM "systemsettings_date" WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE') = 0
THEN
gentime = TIMEOFDAY();
RAISE NOTICE 'Generating new effective rates. This may take several minutes. Process Started At %', gentime;
-- Generate Effective international Rate Sheet
TRUNCATE TABLE "effectivetollfreeoriginationratemaster";

INSERT INTO "effectivetollfreeoriginationratemaster" 
SELECT ratesheet.CustomerID, ratesheet.BilledPrefix, RetailRate FROM "tollfreeoriginationratemaster" AS ratesheet
INNER JOIN (SELECT CustomerID, BilledPrefix, MAX(effectivedate) AS effectivedate FROM "tollfreeoriginationratemaster" WHERE effectivedate <= CDRDate GROUP BY CustomerID, BilledPrefix) AS inaffect
ON ratesheet.CustomerID = inaffect.CustomerID AND ratesheet.BilledPrefix = inaffect.BilledPrefix AND ratesheet.effectivedate = inaffect. EffectiveDate;
GET DIAGNOSTICS rec_count = ROW_COUNT;

DELETE FROM "systemsettings_date" WHERE SettingName = 'TFORIG_EFFECTIVE_RATE_DATE';
INSERT INTO "systemsettings_date" VALUES ('TFORIG_EFFECTIVE_RATE_DATE', CDRDate);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Generation of new rates completed at %', age(EndDateTime,gentime);

INSERT INTO "processhistory" VALUES('Generating TollFree Origination Rate Sheet',gentime,EndDateTime,age(EndDateTime,gentime),rec_count);
END IF;

-- create temporary processing table 
CREATE TEMPORARY TABLE cdrtforig (
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
		BilledPrefix varchar(10), 
		RetailRate numeric(9,7),
		RetailPrice numeric(19,7),
		CarrierID varchar(100),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),
        RowID serial4 UNIQUE NOT NULL
)ON COMMIT DROP;

-- collect cdr to be rated now. The rate can be changed in the variable PROCESSING_LIMIT
RAISE NOTICE 'Gathering new records to rate.'; gentime = TIMEOFDAY();

INSERT INTO cdrtforig (callid, customerid, calltype, calldatetime, duration, billedduration, direction, sourceip, originatingnumber, destinationnumber, billedprefix, retailrate, retailprice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, null, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, null, CarrierID, wholesalerate, wholesaleprice
FROM "callrecordmaster_tbr" 
WHERE cast(CallDateTime as date) = CDRDate AND CallType = 30
LIMIT PROCESSING_LIMIT;
GET DIAGNOSTICS rec_count = ROW_COUNT;
RAISE NOTICE '% new records to be rated.', rec_count;
/*
EXCEPTION
WHEN OTHERS THEN
RAISE EXCEPTION 'Error Gathering Records. Aborted.';
RETURN 00000; 
END IF;
*/
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



--find customerid by ip address.
RAISE NOTICE 'Retrieving customerID based on IP address.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET customerid = "ipaddressmaster".customerid 
FROM "ipaddressmaster"
WHERE cdrtforig.sourceip = "ipaddressmaster".ipaddress and cdrtforig.customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);



-- move all records to HELD that we could not find the customerid for
RAISE NOTICE 'Moving invalid customerid to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "callrecordmaster_held" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'Invalid CustomerID.'
FROM cdrtforig where customerid = '' AND CallID not in (SELECT CallID FROM "callrecordmaster_held");

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig where customerid = '');

DELETE FROM cdrtforig where customerid = '';
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);


-- create relevant indexes now that table is populated
RAISE NOTICE 'Creating index cdrtforig_rawduration.'; gentime = TIMEOFDAY();
CREATE INDEX cdrtforig_rawduration ON cdrtforig (Duration);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

BEGIN
RAISE NOTICE 'Deleting 0 duration.'; gentime = TIMEOFDAY();
-- delete 0 duration
DELETE FROM cdrtforig WHERE duration <= 0;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate billable duration
RAISE NOTICE 'Calculating Billable Duration.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET BilledDuration = 6 WHERE duration <= 6;
UPDATE cdrtforig SET BilledDuration = duration WHERE duration % 6 = 0 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 5 WHERE duration % 6 = 1 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 4 WHERE duration % 6 = 2 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 3 WHERE duration % 6 = 3 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 2 WHERE duration % 6 = 4 AND BilledDuration IS NULL;
UPDATE cdrtforig SET BilledDuration = duration + 1 WHERE duration % 6 = 5 AND BilledDuration IS NULL;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get retail rates
RAISE NOTICE 'Getting retail rates.'; gentime = TIMEOFDAY();

FOR i IN REVERSE 10..2 LOOP
UPDATE cdrtforig SET billedprefix = "effectivetollfreeoriginationratemaster".billedprefix, retailrate = "effectivetollfreeoriginationratemaster".retailrate 
FROM "effectivetollfreeoriginationratemaster"
WHERE cdrtforig.customerid = "effectivetollfreeoriginationratemaster".customerid and SUBSTRING(cdrtforig.originatingnumber FROM 1 FOR i) = "effectivetollfreeoriginationratemaster".billedprefix and cdrtforig.retailrate is NULL;
END LOOP;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- get default retail rates
RAISE NOTICE 'Getting default rates.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET retailrate = "effectivetollfreeoriginationratemaster".retailrate, billedprefix = '*' 
FROM "effectivetollfreeoriginationratemaster"
WHERE  cdrtforig.customerid = "effectivetollfreeoriginationratemaster".customerid AND "effectivetollfreeoriginationratemaster".billedprefix = '*' AND
cdrtforig.retailrate is null;
EndDateTime = TIMEOFDAY(); 
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move all records to HELD that we could not find the rate for
RAISE NOTICE 'Moving unrated records to HELD table.'; gentime = TIMEOFDAY();
INSERT INTO "callrecordmaster_held" (CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, CarrierID, wholesalerate, wholesaleprice, ErrorMessage) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, null, null, CarrierID, wholesalerate, wholesaleprice, 'No rate found.'
FROM cdrtforig where RetailRate is null AND CallID not in (SELECT CallID FROM "callrecordmaster_held");

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig WHERE RetailRate is null);

DELETE FROM cdrtforig WHERE RetailRate is null;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate retail price
RAISE NOTICE 'Calculating retail price.'; gentime = TIMEOFDAY();
UPDATE cdrtforig SET RetailPrice = RetailRate * BilledDuration / 60.00000;
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- move rated records to the CallRecordMaster
RAISE NOTICE 'Moving rated records to the master table.'; gentime = TIMEOFDAY();
INSERT INTO "CallRecordMaster" (CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, LRNDipFee, BilledNumber, RatedDateTime, RetailRate, CNAMDipped, CNAMFee, RetailPrice, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, BilledDuration, 'I', SourceIP, OriginatingNumber, DestinationNumber, null, 0, BilledPrefix, current_timestamp, RetailRate, null, 0, RetailPrice, CarrierID, wholesalerate, wholesaleprice
FROM cdrtforig; -- WHERE CallID not in (SELECT CallID FROM "CallRecordMaster"); This can go in as a failsafe, but will slow things down

DELETE FROM "callrecordmaster_tbr" WHERE CallID in (SELECT CallID FROM cdrtforig);
EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,gentime);

-- calculate/display how long the process took
EndDateTime = TIMEOFDAY();

RAISE NOTICE 'Function took %', age(EndDateTime,StartDateTime);

INSERT INTO "processhistory" VALUES('fnRateTollFreeOriginationCDR', StartDateTime, EndDateTime, age(EndDateTime, StartDateTime), rec_count);

END;



RETURN 00000;

END;


$$ LANGUAGE plpgsql;
