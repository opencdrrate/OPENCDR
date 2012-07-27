DROP TABLE customerretailplanmaster;
CREATE TABLE customerretailplanmaster(
		CustomerID varchar(15) PRIMARY KEY,
                PlanID varchar(15) NOT NULL,
		ActivationDate date NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);