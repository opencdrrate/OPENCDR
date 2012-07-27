DROP TABLE retailplanmaster;
CREATE TABLE retailplanmaster(
		PlanID varchar(15) PRIMARY KEY,
		PlanDescription varchar(100) NOT NULL,
                ServiceFee numeric(9, 2) NOT NULL DEFAULT 0,
		OriginationRate numeric(9,7) NOT NULL DEFAULT 0,
		TollFreeOriginationRate numeric(9,9) NOT NULL DEFAULT 0,
                FreeMinutesPerCycle integer NOT NULL DEFAULT 0,
		RowID serial4 UNIQUE NOT NULL
);