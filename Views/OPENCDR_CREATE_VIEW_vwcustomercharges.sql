create or replace view vwcustomercharges

as

select a.customerid, sum(b.lineitemamount) as "TotalCharges" from customermaster as a inner join billingbatchdetails as b on a.customerid = b.customerid group by a.customerid;