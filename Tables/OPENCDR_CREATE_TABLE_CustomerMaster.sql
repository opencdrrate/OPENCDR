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

DROP TABLE customermaster;
CREATE TABLE customermaster(
		CustomerID varchar(15) PRIMARY KEY,
		CustomerName varchar(100) NOT NULL,
		LRNDipRate numeric(9,9) NOT NULL DEFAULT 0,
		CNAMDipRate numeric(9,9) NOT NULL DEFAULT 0,
		IndeterminateJurisdictionCallType smallint CHECK (IndeterminateJurisdictionCallType = 5 OR IndeterminateJurisdictionCallType = 10),
		BillingCycle varchar(15),
		RowID serial4 UNIQUE NOT NULL
);