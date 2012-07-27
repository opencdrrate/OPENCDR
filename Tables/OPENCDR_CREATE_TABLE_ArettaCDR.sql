DROP TABLE arettacdr;
CREATE TABLE arettacdr(
        CallDate timestamp NOT NULL,
        Trunk varchar(100),
        CallType varchar(100),
        Source_CallerID varchar(50) NOT NULL,  
        Destination varchar(50) NOT NULL,              
        Sec integer NOT NULL,
        Disposition varchar(100),
	Cost numeric(9, 4),
        RowID serial4 UNIQUE NOT NULL
);