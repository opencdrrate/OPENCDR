DROP TABLE customermaster;
CREATE TABLE customermaster(
		CustomerID varchar(15) PRIMARY KEY,
		CustomerName varchar(100) NOT NULL,
		LRNDipRate numeric(9,9) NOT NULL DEFAULT 0,
		CNAMDipRate numeric(9,9) NOT NULL DEFAULT 0,
		IndeterminateJurisdictionCallType smallint CHECK (IndeterminateJurisdictionCallType = 5 OR IndeterminateJurisdictionCallType = 10),
		BillingCycle varchar(15),
                CustomerType varchar(50),
                CreditLimit numeric(9, 2),
		RowID serial4 UNIQUE NOT NULL
);