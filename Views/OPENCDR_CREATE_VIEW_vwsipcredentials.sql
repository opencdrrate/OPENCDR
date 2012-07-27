CREATE OR REPLACE VIEW vwsipcredentials AS  

SELECT customerid, sipusername, sippassword, status as "sipstatus" from customersipcredentials where sipusername <> ''
UNION ALL
SELECT customerid, sipusername, sippassword, sipstatus from didmaster where sipusername <> ''
;
