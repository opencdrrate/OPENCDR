CREATE OR REPLACE VIEW vwcallspermonthpercarrier AS  SELECT date_part('year'::text, callrecordmaster.calldatetime) AS "Year", date_part('month'::text, callrecordmaster.calldatetime) AS "Month", callrecordmaster.carrierid, callrecordmaster.direction, count(*) AS "Calls", sum(callrecordmaster.duration) / 60 AS "RawDuration", sum(callrecordmaster.billedduration) / 60 AS "BilledDuration"
   FROM callrecordmaster
  GROUP BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction
  ORDER BY date_part('year'::text, callrecordmaster.calldatetime), date_part('month'::text, callrecordmaster.calldatetime), callrecordmaster.carrierid, callrecordmaster.direction;
