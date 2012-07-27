DROP TABLE bandwidthcdr;
CREATE TABLE bandwidthcdr(
        DEBTOR_ID varchar(15) NOT NULL,
        itemid varchar(100) NOT NULL,
        billable_minutes numeric(19, 6) NOT NULL,
        itemtype varchar(100) NOT NULL,  
        trans_rate numeric(19, 7),  
        amount numeric(19, 7), 
        record_date timestamp without time zone NOT NULL,
        src varchar(50) NOT NULL,
        dest varchar(50) NOT NULL,
        dst_rcs varchar(50),
        lrn varchar(50),
        CustomerID varchar(15),
        Direction char(1),
        CallType smallint, 
        RawDuration int,
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);