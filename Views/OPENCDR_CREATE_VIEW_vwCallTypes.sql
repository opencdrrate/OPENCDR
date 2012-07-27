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
UNION ALL
SELECT 40 as "CallType", 'Toll-free Termination' as "CallTypeDesc"
UNION ALL
SELECT 60 as "CallType", 'Retail Termination' as "CallTypeDesc"
UNION ALL
SELECT 65 as "CallType", 'Retail Origination' as "CallTypeDesc"
UNION ALL
SELECT 68 as "CallType", 'Retail Origination (Toll-free)' as "CallTypeDesc"
;
