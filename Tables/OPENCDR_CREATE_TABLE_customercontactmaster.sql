DROP TABLE customercontactmaster;
CREATE TABLE customercontactmaster(
		CustomerID varchar(15) PRIMARY KEY,
		PrimaryEmailAddress varchar(100) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);