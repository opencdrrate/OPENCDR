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

DROP TABLE onetimechargequeue;
CREATE TABLE onetimechargequeue(
        OneTimeChargeID serial4 UNIQUE NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        ChargeDate date NOT NULL,
        UnitAmount numeric(9, 2) NOT NULL,
        Quantity numeric(9, 2) NOT NULL,
		ChargeDesc varchar(100) NOT NULL,
		BillingBatchID varchar(15)	
);