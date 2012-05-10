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

CREATE OR REPLACE VIEW vwcallspermonthpercarrier AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction;
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

CREATE OR REPLACE VIEW vwcallspermonthpercarrierratecenter AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction, callrecordmaster.ratecenter;
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
;
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

create or replace view vwcustomercharges

as

select a.customerid, sum(b.lineitemamount) as "TotalCharges" from customermaster as a inner join billingbatchdetails as b on a.customerid = b.customerid group by a.customerid;--======================================================================--
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

create or replace view vwcustomerpayments

as

select a.customerid, sum(b.PaymentAmount) as "TotalPayments" from customermaster as a inner join paymentmaster as b on a.customerid = b.customerid group by a.customerid;