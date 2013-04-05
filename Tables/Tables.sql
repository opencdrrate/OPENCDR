DROP TABLE arettacdr;
CREATE TABLE arettacdr(
        CallDate timestamp NOT NULL,
        Trunk varchar(100),
        CallType varchar(100),
        Source_CallerID varchar(50) NOT NULL,  
        Destination varchar(50) NOT NULL,              
        Sec integer NOT NULL,
        Disposition varchar(100),
	Cost numeric(9, 4),
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE asteriskcdr;
CREATE TABLE asteriskcdr(
        accountcode varchar(40) NOT NULL,
        src varchar(80) NOT NULL,
        dst varchar(80) NOT NULL,
        answer timestamp without time zone,  
        billsec int,    
        disposition varchar(100),          
        uniqueid varchar(32) NOT NULL PRIMARY KEY,
        RowID serial4 UNIQUE NOT NULL
);CREATE TABLE asteriskserversetup(
        ServerName varchar(50) PRIMARY KEY,
	ServerIPOrDNS varchar(100) NOT NULL,
        MySQLPort int NOT NULL DEFAULT 3306,
        MySQLLogin varchar(50) NOT NULL,
        MySQLPassword varchar(50) NOT NULL,
        CDRDatabase varchar(50) NOT NULL DEFAULT 'asteriskcdrdb',
        CDRTable varchar(50) NOT NULL DEFAULT 'cdr',
        Active smallint NOT NULL DEFAULT 1,
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE bandwidthcdr;
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
);DROP TABLE billingbatchdetails;
CREATE TABLE billingbatchdetails(
	BillingBatchID varchar(15) NOT NULL,
	CustomerID varchar(15) NOT NULL,
	CallType smallint,
	LineItemType smallint NOT NULL,
	LineItemDesc varchar(100) NOT NULL,
	LineItemAmount numeric(9,2) NOT NULL,
	LineItemQuantity integer NOT NULL,
	PeriodStartDate date,
	PeriodEndDate date,
	RowID serial4 UNIQUE NOT NULL
);DROP TABLE billingbatchmaster;
CREATE TABLE billingbatchmaster(
	BillingBatchID varchar(15) PRIMARY KEY,
	BillingDate date NOT NULL, 
	DueDate date NOT NULL,
	BillingCycleID varchar(15) NOT NULL,
	UsagePeriodEnd date NOT NULL,
	RowID serial4 UNIQUE NOT NULL
);DROP TABLE callrecordmaster;
CREATE TABLE callrecordmaster(
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
	BilledDuration integer NOT NULL,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN      varchar(50),
	LRNDipFee numeric(9,9) NOT NULL DEFAULT 0,
	BilledNumber varchar(50) NOT NULL,
	BilledPrefix varchar(10),
	RatedDateTime timestamp NOT NULL,
	RetailRate numeric(9,7) NOT NULL,		
        CNAMDipped boolean,
	CNAMFee numeric (9,9) NOT NULL DEFAULT 0,
	BilledTier smallint,
	RateCenter varchar(50),
	BillingBatchID Varchar(15),
	RetailPrice numeric(19,7) NOT NULL,
	CarrierID varchar(100),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),	
        RoutingPrefix varchar(10),
        CanBeFree boolean,	
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE callrecordmaster_held;
CREATE TABLE callrecordmaster_held(
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15),
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN varchar(50),
        CNAMdipped boolean,
	RateCenter varchar(50),
	CarrierID varchar(100),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),	
        RoutingPrefix varchar(10),	
	ErrorMessage varchar(100) NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE callrecordmaster_tbr;
CREATE TABLE callrecordmaster_tbr(
        CallID varchar(100)     PRIMARY KEY,
        CustomerID varchar(15),
        CallType smallint,
        CallDateTime timestamp NOT NULL,
        Duration integer NOT NULL,
        Direction char(1),
        SourceIP varchar(15),
        OriginatingNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        LRN varchar(50),
        CNAMdipped boolean,
	RateCenter varchar(50),
	CarrierID varchar(100),
	WholesaleRate numeric(19, 7),
	WholesalePrice numeric(19, 7),
        RoutingPrefix varchar(10),
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE concurrentcalls;
CREATE TABLE concurrentcalls(
        CallDateTime timestamp without time zone NOT NULL PRIMARY KEY,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE concurrentcallsdirection;
CREATE TABLE concurrentcallsdirection(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction)
);DROP TABLE concurrentcallsdirectioncarrier;
CREATE TABLE concurrentcallsdirectioncarrier(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        CarrierID varchar(100) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction, CarrierID)
);DROP TABLE concurrentcallsdirectionratecenter;
CREATE TABLE concurrentcallsdirectionratecenter(
        CallDateTime timestamp without time zone NOT NULL,
        Direction char(1) NOT NULL,
        RateCenter varchar(50) NOT NULL,
        ConcurrentCalls integer NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CallDateTime, Direction, RateCenter)
);DROP TABLE customerbillingaddressmaster;
CREATE TABLE customerbillingaddressmaster(
		CustomerID varchar(15) PRIMARY KEY,
		Address1 varchar(100) NOT NULL,
		Address2 varchar(100) NOT NULL,
		City varchar(100) NOT NULL,
		StateOrProv varchar(100) NOT NULL,
		Country varchar(100) NOT NULL,
		ZipCode varchar(15) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE customercontactmaster;
CREATE TABLE customercontactmaster(
		CustomerID varchar(15) PRIMARY KEY,
		PrimaryEmailAddress varchar(100) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE customermaster;
CREATE TABLE customermaster(
		CustomerID varchar(15) PRIMARY KEY,
		CustomerName varchar(100) NOT NULL,
		LRNDipRate numeric(9,9) NOT NULL DEFAULT 0,
		CNAMDipRate numeric(9,9) NOT NULL DEFAULT 0,
		IndeterminateJurisdictionCallType smallint CHECK (IndeterminateJurisdictionCallType = 5 OR IndeterminateJurisdictionCallType = 10),
		BillingCycle varchar(15),
                CustomerType varchar(50),
                CreditLimit numeric(9, 2),
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE customerretailplanmaster;
CREATE TABLE customerretailplanmaster(
		CustomerID varchar(15) PRIMARY KEY,
                PlanID varchar(15) NOT NULL,
		ActivationDate date NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE customersipcredentials;
CREATE TABLE customersipcredentials(
		CustomerID varchar(15) PRIMARY KEY,
		SIPUsername varchar(100) NOT NULL,
		SIPPassword varchar(100) NOT NULL,
		Status smallint NOT NULL DEFAULT 1,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE customertaxsetup;
CREATE TABLE customertaxsetup(
	CustomerID varchar(15) NOT NULL,
	CallType smallint NOT NULL,
	TaxType varchar(50) NOT NULL,
	TaxRate numeric(9,7) NOT NULL,
	RowID serial4 UNIQUE NOT NULL,
	PRIMARY KEY(CustomerID, CallType, TaxType)
);DROP TABLE didmaster;
CREATE TABLE didmaster(
		DID varchar(15) PRIMARY KEY,
		CustomerID varchar(15) not null,
                SIPUsername varchar(100),
                SIPPassword varchar(100),
                SIPStatus smallint NOT NULL DEFAULT 1,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE effectiveinternationalratemaster;
CREATE TABLE effectiveinternationalratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix)
);DROP TABLE effectiveinterstateratemaster;
CREATE TABLE effectiveinterstateratemaster(
		CustomerID varchar(15) NOT NULL,
		NPANXXX varchar(7) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, NPANXXX)
);DROP TABLE effectiveintrastateratemaster;
CREATE TABLE effectiveintrastateratemaster(
		CustomerID varchar(15) NOT NULL,
		NPANXXX varchar(7) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, NPANXXX)
);DROP TABLE effectivesimpleterminationratemaster;
CREATE TABLE effectivesimpleterminationratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix)
);DROP TABLE effectivetollfreeoriginationratemaster;
CREATE TABLE effectivetollfreeoriginationratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix)
);DROP TABLE internationalratemaster;
CREATE TABLE internationalratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix, EffectiveDate)
);DROP TABLE interstateratemaster;
CREATE TABLE interstateratemaster(
        CustomerID varchar(15) NOT NULL,
        EffectiveDate date NOT NULL,
        NPANXXX char(7) NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CustomerID, EffectiveDate, NPANXXX)
);DROP TABLE intrastateratemaster;
CREATE TABLE intrastateratemaster(
        CustomerID varchar(15) NOT NULL,
        EffectiveDate date NOT NULL,
        NPANXXX char(7) NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CustomerID, EffectiveDate, NPANXXX)
);DROP TABLE ipaddressmaster;
CREATE TABLE ipaddressmaster(
		IPAddress varchar(15) PRIMARY KEY,
		CustomerID varchar(15) not null,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE npamaster;
CREATE TABLE npamaster(
		NPA char(3) PRIMARY KEY,
		State varchar(50) not null,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE onetimechargequeue;
CREATE TABLE onetimechargequeue(
        OneTimeChargeID serial4 UNIQUE NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        ChargeDate date NOT NULL,
        UnitAmount numeric(9, 2) NOT NULL,
        Quantity numeric(9, 2) NOT NULL,
		ChargeDesc varchar(100) NOT NULL,
		BillingBatchID varchar(15)	
);DROP TABLE passwordresetmaster;
CREATE TABLE passwordresetmaster(
	Token varchar(100) NOT NULL PRIMARY KEY,
        username varchar(15) NOT NULL,	
        Expires timestamp NOT NULL,s
        RowID serial4 UNIQUE NOT NULL
);DROP TABLE paymentmaster;
CREATE TABLE paymentmaster(
		CustomerID varchar(15),
		PaymentDate timestamp NOT NULL,
		PaymentAmount numeric(9, 2) NOT NULL CHECK (PaymentAmount > 0),
		PaymentType varchar(20) NOT NULL,
		PaymentNote varchar(100) NOT NULL,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE processhistory;
CREATE TABLE processhistory(
		ProcessName varchar(100) NOT NULL,
		StartDateTime timestamp NOT NULL,
		EndDateTime timestamp NOT NULL,
		Duration interval NOT NULL,
		Records integer,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(ProcessName, StartDateTime)
);﻿DROP TABLE recurringchargemaster;
CREATE TABLE recurringchargemaster(
        RecurringChargeID serial4 UNIQUE NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,
        ActivationDate date NOT NULL,
        DeactivationDate date,
        UnitAmount numeric(9, 2) NOT NULL,
        Quantity numeric(9, 2) NOT NULL,
		ChargeDesc varchar(100) NOT NULL	
);DROP TABLE retailplanmaster;
CREATE TABLE retailplanmaster(
		PlanID varchar(15) PRIMARY KEY,
		PlanDescription varchar(100) NOT NULL,
                ServiceFee numeric(9, 2) NOT NULL DEFAULT 0,
		OriginationRate numeric(9,7) NOT NULL DEFAULT 0,
		TollFreeOriginationRate numeric(9,9) NOT NULL DEFAULT 0,
                FreeMinutesPerCycle integer NOT NULL DEFAULT 0,
		RowID serial4 UNIQUE NOT NULL
);DROP TABLE retailplanterminationratemaster;
CREATE TABLE retailplanterminationratemaster(
		PlanID varchar(15) NOT NULL,
		BilledPrefix varchar(10) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
                CanBeFree boolean NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(PlanID, BilledPrefix, EffectiveDate)
);﻿DROP TABLE simpleterminationratemaster;
CREATE TABLE simpleterminationratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix, EffectiveDate)
);﻿DROP TABLE systemsettings_date;
CREATE TABLE systemsettings_date(
		SettingName varchar(50) PRIMARY KEY,
		SettingValue date
);DROP TABLE systemsettings_string;
CREATE TABLE systemsettings_string(
		SettingName varchar(50) PRIMARY KEY,
		SettingValue varchar(500)
);﻿DROP TABLE thinktelcdr;
CREATE TABLE thinktelcdr(
        SourceNumber varchar(50) NOT NULL,
        DestinationNumber varchar(50) NOT NULL,
        CallDate timestamp without time zone NOT NULL,
        UsageType varchar(100) NOT NULL,  
        RawDuration numeric(9, 1),   
        CustomerID varchar(15),
        Direction char(1),
        CallType smallint, 
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);﻿DROP TABLE tieredoriginationratecentermaster;
CREATE TABLE tieredoriginationratecentermaster(
        RateCenter varchar(50) PRIMARY KEY,
	Tier smallint NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);﻿DROP TABLE tieredoriginationratemaster;
CREATE TABLE tieredoriginationratemaster(
        CustomerID varchar(15) NOT NULL,
		Tier smallint NOT NULL,
        EffectiveDate date NOT NULL,
        RetailRate numeric(9,7) NOT NULL,
        RowID serial4 UNIQUE NOT NULL,
        PRIMARY KEY(CustomerID, Tier, EffectiveDate)
);﻿DROP TABLE tollfreeoriginationratemaster;
CREATE TABLE tollfreeoriginationratemaster(
		CustomerID varchar(15) NOT NULL,
		BilledPrefix varchar(11) NOT NULL,
		EffectiveDate timestamp NOT NULL,
		RetailRate numeric(9,7) NOT NULL,
		RowID serial4 UNIQUE NOT NULL,
		PRIMARY KEY(CustomerID, BilledPrefix, EffectiveDate)
);CREATE TABLE users(
        ID serial4 PRIMARY KEY,
        Username varchar(50) NOT NULL,
        Password varchar(50) NOT NULL,
        Role varchar(20) NOT NULL,
        CustomerID varchar(15),
        Created timestamp NOT NULL,
        Modified timestamp
);﻿DROP TABLE vitelitycdr;
CREATE TABLE vitelitycdr(
        CallDateTime timestamp without time zone NOT NULL,
        Source varchar(50) NOT NULL,
        Destination varchar(50) NOT NULL,  
        Seconds int NOT NULL,  
        CallerID varchar(100),  
        Disposition varchar(100),          
        Cost numeric(19, 16),
        CustomerID varchar(15),
        CallType smallint,
        Direction char(1),
        RowID serial4 UNIQUE NOT NULL PRIMARY KEY
);﻿DROP TABLE voipinnovationscdr;
CREATE TABLE voipinnovationscdr(
	CallType varchar(100) NOT NULL,
        StartTime timestamp NOT NULL,
        CallDuration numeric(19, 5) NOT NULL,
        CustomerIP varchar(20),
        ANI varchar(50) NOT NULL,
        DNIS varchar(50) NOT NULL,
        LRN varchar(50) NOT NULL,
        OrigTier smallint,
        RowID serial4 UNIQUE NOT NULL
);﻿DROP TABLE webportalaccess;
CREATE TABLE webportalaccess(
        Username varchar(100)     PRIMARY KEY,
        Nonce char(10) NOT NULL,
        HashedPassword varchar(500),
        CustomerID varchar(15),		
        RowID serial4 UNIQUE NOT NULL
);﻿DROP TABLE webportalaccesstokens;
CREATE TABLE webportalaccesstokens(
		Token varchar(100) NOT NULL PRIMARY KEY,
        CustomerID varchar(15) NOT NULL,	
        Expires timestamp NOT NULL,
        RowID serial4 UNIQUE NOT NULL
);