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