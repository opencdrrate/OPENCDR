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

DROP TABLE customerbillingaddressmaster;
CREATE TABLE customerbillingaddressmaster(
		CustomerID varchar(15) PRIMARY KEY,
		Address1 varchar(100) NOT NULL,
		Address2 varchar(100) NOT NULL,
		City varchar(100) NOT NULL,
		StateOrProv varchar(100) NOT NULL,
		Country varchar(100) NOT NULL,
		ZipCode varchar(15) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);