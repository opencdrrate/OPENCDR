CREATE OR REPLACE VIEW "vwcalldirection" AS  SELECT callrecordmaster.callid, callrecordmaster.calldatetime, callrecordmaster.customerid, callrecordmaster.carrierid, callrecordmaster.direction, 
        CASE callrecordmaster.direction
            WHEN 'I'::bpchar THEN 1
            ELSE 0
        END AS "InboundCall", 
        CASE callrecordmaster.direction
            WHEN 'I'::bpchar THEN callrecordmaster.duration
            ELSE 0
        END AS "InboundDuration", 
        CASE callrecordmaster.direction
            WHEN 'I'::bpchar THEN callrecordmaster.billedduration
            ELSE 0
        END AS "InboundBilledDuration", 
        CASE callrecordmaster.direction
            WHEN 'O'::bpchar THEN 1
            ELSE 0
        END AS "OutboundCall", 
        CASE callrecordmaster.direction
            WHEN 'O'::bpchar THEN callrecordmaster.duration
            ELSE 0
        END AS "OutboundDuration", 
        CASE callrecordmaster.direction
            WHEN 'O'::bpchar THEN callrecordmaster.billedduration
            ELSE 0
        END AS "OutboundBilledDuration"
   FROM callrecordmaster;
CREATE OR REPLACE VIEW vwcallspermonthpercarrierratecenter AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter;
CREATE OR REPLACE VIEW vwcallspermonthpercarrier AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction;
CREATE OR REPLACE VIEW vwcalltypes AS  

SELECT 5 as "CallType", 'Intrastate' as "CallTypeDesc"
UNION ALL
SELECT 10 as "CallType", 'Interstate' as "CallTypeDesc"
UNION ALL
SELECT 15 as "CallType", 'Tiered Origination' as "CallTypeDesc"
UNION ALL
SELECT 20 as "CallType", 'Termination of Indeterminate Jurisdiction' as "CallTypeDesc"
UNION ALL
SELECT 25 as "CallType", 'International' as "CallTypeDesc"
UNION ALL
SELECT 30 as "CallType", 'Toll-free Origination' as "CallTypeDesc"
UNION ALL
SELECT 35 as "CallType", 'Simple Termination' as "CallTypeDesc"
UNION ALL
SELECT 40 as "CallType", 'Toll-free Termination' as "CallTypeDesc"
UNION ALL
SELECT 60 as "CallType", 'Retail Termination' as "CallTypeDesc"
UNION ALL
SELECT 65 as "CallType", 'Retail Origination' as "CallTypeDesc"
UNION ALL
SELECT 68 as "CallType", 'Retail Origination (Toll-free)' as "CallTypeDesc"
;
create or replace view vwcustomercharges

as

select a.customerid, sum(b.lineitemamount) as "TotalCharges" from customermaster as a inner join billingbatchdetails as b on a.customerid = b.customerid group by a.customerid;create or replace view vwcustomerpayments

as

select a.customerid, sum(b.PaymentAmount) as "TotalPayments" from customermaster as a inner join paymentmaster as b on a.customerid = b.customerid group by a.customerid;create or replace view vwcustomerunbilledcdr

as

select a.customerid, sum(b.retailprice) as "UnbilledCDR" from customermaster as a inner join callrecordmaster as b on a.customerid = b.customerid where b.BillingBatchID is null group by a.customerid;CREATE OR REPLACE VIEW vwsipcredentials AS  

SELECT customerid, sipusername, sippassword, status as "sipstatus" from customersipcredentials where sipusername <> ''
UNION ALL
SELECT customerid, sipusername, sippassword, sipstatus from didmaster where sipusername <> ''
;
