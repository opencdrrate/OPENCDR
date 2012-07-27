DROP TABLE didmaster;
CREATE TABLE didmaster(
		DID varchar(15) PRIMARY KEY,
		CustomerID varchar(15) not null,
                SIPUsername varchar(100),
                SIPPassword varchar(100),
                SIPStatus smallint NOT NULL DEFAULT 1,
		RowID serial4 UNIQUE NOT NULL
);