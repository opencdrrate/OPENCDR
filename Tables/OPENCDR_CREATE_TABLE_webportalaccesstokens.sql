DROP TABLE webportalaccesstokens;
CREATE TABLE webportalaccesstokens(
		Token varchar(100) NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,	
        Expires timestamp NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);