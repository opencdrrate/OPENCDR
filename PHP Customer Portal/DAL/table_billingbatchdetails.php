<?php
/*
CREATE TABLE billingbatchdetails
(
  billingbatchid character varying(15) NOT NULL,
  customerid character varying(15) NOT NULL,
  calltype smallint NOT NULL,
  lineitemtype smallint NOT NULL,
  lineitemdesc character varying(100) NOT NULL,
  lineitemamount numeric(9,2) NOT NULL,
  lineitemquantity integer NOT NULL,
  periodstartdate date NOT NULL,
  periodenddate date NOT NULL
)
*/
/*
billingbatchid, customerid, calltype, lineitemtype, lineitemdesc, 
       lineitemamount, lineitemquantity, periodstartdate, periodenddate
*/

class psql_billingbatchdetails extends SQLTable{
	public $table_name = 'billingbatchdetails';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	private $batchesStatement;
	
	function psql_billingbatchdetails($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(billingbatchid, customerid, calltype, 
									lineitemtype, lineitemdesc,lineitemamount, 
									lineitemquantity, periodstartdate, periodenddate)
			VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9);
HEREDOC;
		$this->selectStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE customerid = $1;
HEREDOC;
		$this->batchesStatement = <<< HEREDOC
		SELECT billingbatchid,sum(lineitemamount) as totalamount, count(lineitemamount) as items
		FROM billingbatchdetails where customerid = $1 group by billingbatchid, customerid;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertbillingbatchdetails", $this->insertStatement);
		pg_prepare($this->db, "selectbillingbatchdetails", $this->selectStatement);
		pg_prepare($this->db, "selectbatches", $this->batchesStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*billingbatchid, customerid, calltype, lineitemtype, lineitemdesc, 
       lineitemamount, lineitemquantity, periodstartdate, periodenddate*/
		$billingbatchid = '';
		$customerid = '';
		$calltype = '';
		$lineitemtype = '';
		$lineitemdesc = '';
		$lineitemamount = '';
		$lineitemquantity = '';
		$periodstartdate = '';
		$periodenddate = '';
		
		if(isset($row['billingbatchid'])){
			$billingbatchid = $row['billingbatchid'];
		}
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['calltype'])){
			$calltype = $row['calltype'];
		}
		if(isset($row['lineitemtype'])){
			$lineitemtype = $row['lineitemtype'];
		}
		if(isset($row['lineitemdesc'])){
			$lineitemdesc = $row['lineitemdesc'];
		}
		if(isset($row['lineitemamount'])){
			$lineitemamount = $row['lineitemamount'];
		}
		if(isset($row['lineitemquantity'])){
			$lineitemquantity = $row['lineitemquantity'];
		}
		if(isset($row['periodstartdate'])){
			$periodstartdate = $row['periodstartdate'];
		}
		if(isset($row['periodenddate'])){
			$periodenddate = $row['periodenddate'];
		}
		
		
		$insertParams = array($billingbatchid,$customerid,$calltype,$lineitemtype,
		$lineitemdesc,$lineitemamount,$lineitemquantity,$periodstartdate,$periodenddate);
		
		$result = pg_execute($this->db, "insertbillingbatchdetails", $insertParams);
		if($result){
			return true;
		}
		return $result;
	}
	function Delete($row){
		throw new Exception('This function is not implemented yet');
	}
	function DoesExist($row){
		throw new Exception('This function is not implemented yet');
	}
	function Update($old, $new){
		throw new Exception('This function is not implemented yet');
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
	
	function Select($customerid){
		$execute_result = pg_execute($this->db, "selectbillingbatchdetails", array($customerid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	function GetBatches($customerid){
		$execute_result = pg_execute($this->db, "selectbatches", array($customerid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
}
?>