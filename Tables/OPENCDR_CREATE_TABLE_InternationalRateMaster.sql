DROP TABLE internationalratemaster;
CREATE TABLE internationalratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix, EffectiveDate)
);