DROP TABLE npamaster;
CREATE TABLE npamaster(
		NPA char(3) PRIMARY KEY,
		State varchar(50) not null,
		RowID serial4 UNIQUE NOT NULL
);