DROP TABLE concurrentcallsdirection;
CREATE TABLE concurrentcallsdirection(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction)
);