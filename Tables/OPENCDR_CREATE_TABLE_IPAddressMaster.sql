DROP TABLE ipaddressmaster;
CREATE TABLE ipaddressmaster(
		IPAddress varchar(15) PRIMARY KEY,
		CustomerID varchar(15) not null,
		RowID serial4 UNIQUE NOT NULL
);