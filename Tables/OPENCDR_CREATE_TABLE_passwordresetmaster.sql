DROP TABLE passwordresetmaster;
CREATE TABLE passwordresetmaster(
	Token varchar(100) NOT NULL PRIMARY KEY,
        username varchar(15) NOT NULL,	
        Expires timestamp NOT NULL,s
        RowID serial4 UNIQUE NOT NULL
);