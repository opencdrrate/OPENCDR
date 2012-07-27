DROP TABLE onetimechargequeue;
CREATE TABLE onetimechargequeue(
        OneTimeChargeID serial4 UNIQUE NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        ChargeDate date NOT NULL,
        UnitAmount numeric(9, 2) NOT NULL,
        Quantity numeric(9, 2) NOT NULL,
		ChargeDesc varchar(100) NOT NULL,
		BillingBatchID varchar(15)	
);