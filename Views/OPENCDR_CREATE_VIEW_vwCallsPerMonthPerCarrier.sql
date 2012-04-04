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

CREATE OR REPLACE VIEW vwcallspermonthpercarrier AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction;
