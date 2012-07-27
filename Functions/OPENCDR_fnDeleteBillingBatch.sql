CREATE OR REPLACE FUNCTION "fnDeleteBillingBatch"(IN billingbatchid_in "varchar") RETURNS int AS $$


DECLARE 


BEGIN

DELETE FROM billingbatchdetails WHERE billingbatchid = billingbatchid_in;

DELETE FROM billingbatchmaster WHERE billingbatchid = billingbatchid_in;

update onetimechargequeue set billingbatchid = null where billingbatchid = billingbatchid_in;

UPDATE callrecordmaster SET billingbatchid = null WHERE billingbatchid = billingbatchid_in;

RETURN 00000;

END;


$$ LANGUAGE plpgsql;

