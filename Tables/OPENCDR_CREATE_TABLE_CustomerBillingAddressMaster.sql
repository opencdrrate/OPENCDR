DROP TABLE customerbillingaddressmaster;
CREATE TABLE customerbillingaddressmaster(
		CustomerID varchar(15) PRIMARY KEY,
		Address1 varchar(100) NOT NULL,
		Address2 varchar(100) NOT NULL,
		City varchar(100) NOT NULL,
		StateOrProv varchar(100) NOT NULL,
		Country varchar(100) NOT NULL,
		ZipCode varchar(15) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);