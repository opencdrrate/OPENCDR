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
