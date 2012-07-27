DROP TABLE retailplanterminationratemaster;
CREATE TABLE retailplanterminationratemaster(
		PlanID varchar(15) NOT NULL,
		BilledPrefix varchar(10) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
                CanBeFree boolean NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(PlanID, BilledPrefix, EffectiveDate)
);