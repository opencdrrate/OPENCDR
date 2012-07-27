DROP TABLE concurrentcallsdirectionratecenter;
CREATE TABLE concurrentcallsdirectionratecenter(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        RateCenter varchar(50) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction, RateCenter)
);