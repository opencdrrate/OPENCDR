create or replace view vwcustomerpayments

as

select a.customerid, sum(b.PaymentAmount) as "TotalPayments" from customermaster as a inner join paymentmaster as b on a.customerid = b.customerid group by a.customerid;