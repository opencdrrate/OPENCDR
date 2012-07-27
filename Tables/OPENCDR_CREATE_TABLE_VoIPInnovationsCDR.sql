DROP TABLE voipinnovationscdr;
CREATE TABLE voipinnovationscdr(
	CallType varchar(100) NOT NULL,
        StartTime timestamp NOT NULL,
        CallDuration numeric(19, 5) NOT NULL,
        CustomerIP varchar(20),
        ANI varchar(50) NOT NULL,
        DNIS varchar(50) NOT NULL,
        LRN varchar(50) NOT NULL,
        OrigTier smallint,
        RowID serial4 UNIQUE NOT NULL
);