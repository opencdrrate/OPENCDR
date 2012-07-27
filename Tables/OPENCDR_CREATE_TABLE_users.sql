CREATE TABLE users(
        ID serial4 PRIMARY KEY,
        Username varchar(50) NOT NULL,
        Password varchar(50) NOT NULL,
        Role varchar(20) NOT NULL,
        CustomerID varchar(15),
        Created timestamp NOT NULL,
        Modified timestamp
);