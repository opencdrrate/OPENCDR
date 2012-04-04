--======================================================================--
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
