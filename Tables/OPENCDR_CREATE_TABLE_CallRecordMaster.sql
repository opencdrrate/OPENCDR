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

DROP TABLE callrecordmaster;
CREATE TABLE callrecordmaster(
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
		BilledDuration integer NOT NULL,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN      varchar(50),
		LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
		BilledNumber varchar(50) NOT NULL,
		BilledPrefix varchar(10),
		RatedDateTime timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,		
        CNAMDipped boolean,
		CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
		BilledTier smallint,
		RateCenter varchar(50),
		BillingBatchID Varchar(15),
		RetailPrice numeric(19,7) NOT NULL,
		CarrierID varchar(100),
		WholesaleRate numeric(19, 7),
		WholesalePrice numeric(19, 7),		
        RowID serial4 UNIQUE NOT NULL
);