DROP TABLE effectivetollfreeoriginationratemaster;
CREATE TABLE effectivetollfreeoriginationratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix)
);