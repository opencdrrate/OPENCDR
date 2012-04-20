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

CREATE OR REPLACE FUNCTION "fnMoveHELDCDRToTBR"() RETURNS int AS $$

DECLARE
PROCESSING_LIMIT int = 100000; -- how many calls to move at a time
StartDateTime timestamp = TIMEOFDAY();
EndDateTime timestamp;
BEGIN

RAISE NOTICE 'Function started at %', StartDateTime;
-- create temporary processing table 
CREATE TEMPORARY TABLE tmp_held (
        CallID varchar(100)     PRIMARY KEY
)ON COMMIT DROP;

-- moving the oldest "callid" from HELD into that table. Edit PROCESSING_LIMIT to change how many.
INSERT INTO tmp_held (CallID) SELECT CallID FROM callrecordmaster_held ORDER BY CallDateTime LIMIT PROCESSING_LIMIT;

INSERT INTO callrecordmaster_tbr (callid,customerid,calltype,calldatetime,duration,direction,sourceip,originatingnumber,destinationnumber,lrn,cnamdipped,ratecenter, CarrierID, wholesalerate, wholesaleprice) 
SELECT CallID, CustomerID, CallType, CallDateTime, Duration, Direction, SourceIP, OriginatingNumber, DestinationNumber, LRN, CNAMdipped, ratecenter, CarrierID, wholesalerate, wholesaleprice FROM callrecordmaster_held WHERE callid IN (SELECT callid FROM tmp_held);

DELETE FROM callrecordmaster_held WHERE CallID IN (SELECT callid FROM tmp_held);

EndDateTime = TIMEOFDAY();
RAISE NOTICE 'Completed in: %', age(EndDateTime,StartDateTime);

RETURN 00000;

END;

$$ LANGUAGE plpgsql;