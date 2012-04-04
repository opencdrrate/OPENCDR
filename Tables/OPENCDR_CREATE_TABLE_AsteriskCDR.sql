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

DROP TABLE asteriskcdr;
CREATE TABLE asteriskcdr(
        accountcode varchar(40) NOT NULL,
        src varchar(80) NOT NULL,
        dst varchar(80) NOT NULL,
        answer timestamp without time zone,  
        billsec int,    
        disposition varchar(100),          
        uniqueid varchar(32) NOT NULL PRIMARY KEY,
        RowID serial4 UNIQUE NOT NULL
);