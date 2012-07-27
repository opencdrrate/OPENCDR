CREATE TABLE asteriskserversetup(
        ServerName varchar(50) PRIMARY KEY,
	ServerIPOrDNS varchar(100) NOT NULL,
        MySQLPort int NOT NULL DEFAULT 3306,
        MySQLLogin varchar(50) NOT NULL,
        MySQLPassword varchar(50) NOT NULL,
        CDRDatabase varchar(50) NOT NULL DEFAULT 'asteriskcdrdb',
        CDRTable varchar(50) NOT NULL DEFAULT 'cdr',
        Active smallint NOT NULL DEFAULT 1,
        RowID serial4 UNIQUE NOT NULL
);