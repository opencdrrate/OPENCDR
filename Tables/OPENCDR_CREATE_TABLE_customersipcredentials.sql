DROP TABLE customersipcredentials;
CREATE TABLE customersipcredentials(
		CustomerID varchar(15) PRIMARY KEY,
		SIPUsername varchar(100) NOT NULL,
		SIPPassword varchar(100) NOT NULL,
		Status smallint NOT NULL DEFAULT 1,
		RowID serial4 UNIQUE NOT NULL
);