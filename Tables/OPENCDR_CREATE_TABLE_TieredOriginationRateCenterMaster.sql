DROP TABLE tieredoriginationratecentermaster;
CREATE TABLE tieredoriginationratecentermaster(
        RateCenter varchar(50) PRIMARY KEY,
	Tier smallint NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);