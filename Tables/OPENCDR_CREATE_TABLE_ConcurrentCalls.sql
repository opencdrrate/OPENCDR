DROP TABLE concurrentcalls;
CREATE TABLE concurrentcalls(
        CallDateTime timestamp without time zone NOT NULL PRIMARY KEY,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);