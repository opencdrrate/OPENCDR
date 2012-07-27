DROP TABLE webportalaccess;
CREATE TABLE webportalaccess(
        Username varchar(100)     PRIMARY KEY,
        Nonce char(10) NOT NULL,
        HashedPassword varchar(500),
        CustomerID varchar(15),		
        RowID serial4 UNIQUE NOT NULL
);