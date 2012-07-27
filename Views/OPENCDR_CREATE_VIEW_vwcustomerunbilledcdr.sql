create or replace view vwcustomerunbilledcdr

as

select a.customerid, sum(b.retailprice) as "UnbilledCDR" from customermaster as a inner join callrecordmaster as b on a.customerid = b.customerid where b.BillingBatchID is null group by a.customerid;