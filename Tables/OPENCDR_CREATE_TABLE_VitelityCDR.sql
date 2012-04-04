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

DROP TABLE vitelitycdr;
CREATE TABLE vitelitycdr(
        CallDateTime timestamp without time zone NOT NULL,
        Source varchar(50) NOT NULL,
        Destination varchar(50) NOT NULL,  
        Seconds int NOT NULL,  
        CallerID varchar(100),  
        Disposition varchar(100),          
        Cost numeric(19, 16),
        CustomerID varchar(15),
        CallType smallint,
        Direction char(1),
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);