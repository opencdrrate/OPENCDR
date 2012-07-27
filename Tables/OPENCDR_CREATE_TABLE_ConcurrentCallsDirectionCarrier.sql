DROP TABLE concurrentcallsdirectioncarrier;
CREATE TABLE concurrentcallsdirectioncarrier(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        CarrierID varchar(100) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction, CarrierID)
);