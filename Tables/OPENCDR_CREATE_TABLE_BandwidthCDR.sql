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

DROP TABLE bandwidthcdr;
CREATE TABLE bandwidthcdr(
        DEBTOR_ID varchar(15) NOT NULL,
        itemid varchar(100) NOT NULL,
        billable_minutes numeric(19, 6) NOT NULL,
        itemtype varchar(100) NOT NULL,  
        trans_rate numeric(19, 7),  
        amount numeric(19, 7), 
        record_date timestamp without time zone NOT NULL,
        src varchar(50) NOT NULL,
        dest varchar(50) NOT NULL,
        dst_rcs varchar(50),
        lrn varchar(50),
        CustomerID varchar(15),
        Direction char(1),
        CallType smallint, 
        RawDuration int,
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);