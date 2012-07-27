DROP TABLE customertaxsetup;
CREATE TABLE customertaxsetup(
	CustomerID varchar(15) NOT NULL,
	CallType smallint NOT NULL,
	TaxType varchar(50) NOT NULL,
	TaxRate numeric(9,7) NOT NULL,
	RowID serial4 UNIQUE NOT NULL,
	PRIMARY KEY(CustomerID, CallType, TaxType)
);