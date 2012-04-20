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

