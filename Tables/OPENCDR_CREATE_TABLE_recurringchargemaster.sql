DROP TABLE recurringchargemaster;
CREATE TABLE recurringchargemaster(
        RecurringChargeID serial4 UNIQUE NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        ActivationDate date NOT NULL,
        DeactivationDate date,
        UnitAmount numeric(9, 2) NOT NULL,
        Quantity numeric(9, 2) NOT NULL,
		ChargeDesc varchar(100) NOT NULL	
);