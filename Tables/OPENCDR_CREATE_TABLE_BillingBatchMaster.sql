DROP TABLE billingbatchmaster;
CREATE TABLE billingbatchmaster(
	BillingBatchID varchar(15) PRIMARY KEY,
	BillingDate date NOT NULL, 
	DueDate date NOT NULL,
	BillingCycleID varchar(15) NOT NULL,
	UsagePeriodEnd date NOT NULL,
	RowID serial4 UNIQUE NOT NULL
);