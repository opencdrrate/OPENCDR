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
