DROP TABLE processhistory;
CREATE TABLE processhistory(
		ProcessName varchar(100) NOT NULL,
		StartDateTime timestamp NOT NULL,
		EndDateTime timestamp NOT NULL,
		Duration interval NOT NULL,
		Records integer,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(ProcessName, StartDateTime)
);