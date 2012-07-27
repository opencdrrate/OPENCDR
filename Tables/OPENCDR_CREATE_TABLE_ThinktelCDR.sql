DROP TABLE thinktelcdr;
CREATE TABLE thinktelcdr(
        SourceNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        CallDate timestamp without time zone NOT NULL,
        UsageType varchar(100) NOT NULL,  
        RawDuration numeric(9, 1),   
        CustomerID varchar(15),
        Direction char(1),
        CallType smallint, 
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);