DROP TABLE interstateratemaster;
CREATE TABLE interstateratemaster(
        CustomerID varchar(15) NOT NULL,
        EffectiveDate date NOT NULL,
        NPANXXX char(7) NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CustomerID, EffectiveDate, NPANXXX)
);