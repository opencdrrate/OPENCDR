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

CREATE OR REPLACE FUNCTION "fnDeleteBillingBatch"(IN billingbatchid_in "varchar") RETURNS int AS $$


DECLARE 


BEGIN

DELETE FROM billingbatchdetails WHERE billingbatchid = billingbatchid_in;

DELETE FROM billingbatchmaster WHERE billingbatchid = billingbatchid_in;

update onetimechargequeue set billingbatchid = null where billingbatchid = billingbatchid_in;

UPDATE callrecordmaster SET billingbatchid = null WHERE billingbatchid = billingbatchid_in;

RETURN 00000;

END;


$$ LANGUAGE plpgsql;

