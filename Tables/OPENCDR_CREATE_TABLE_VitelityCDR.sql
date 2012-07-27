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