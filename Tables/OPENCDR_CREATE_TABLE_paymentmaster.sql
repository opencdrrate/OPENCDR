DROP TABLE paymentmaster;
CREATE TABLE paymentmaster(
		CustomerID varchar(15),
		PaymentDate timestamp NOT NULL,
		PaymentAmount numeric(9, 2) NOT NULL CHECK (PaymentAmount > 0),
		PaymentType varchar(20) NOT NULL,
		PaymentNote varchar(100) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);