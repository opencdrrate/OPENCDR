DROP TABLE tieredoriginationratemaster;
CREATE TABLE tieredoriginationratemaster(
        CustomerID varchar(15) NOT NULL,
		Tier smallint NOT NULL,
        EffectiveDate date NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CustomerID, Tier, EffectiveDate)
);