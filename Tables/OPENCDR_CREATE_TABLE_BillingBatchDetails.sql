DROP TABLE billingbatchdetails;
CREATE TABLE billingbatchdetails(
	BillingBatchID varchar(15) NOT NULL,
	CustomerID varchar(15) NOT NULL,
	CallType smallint,
	LineItemType smallint NOT NULL,
	LineItemDesc varchar(100) NOT NULL,
	LineItemAmount numeric(9,2) NOT NULL,
	LineItemQuantity integer NOT NULL,
	PeriodStartDate date,
	PeriodEndDate date,
	RowID serial4 UNIQUE NOT NULL
);