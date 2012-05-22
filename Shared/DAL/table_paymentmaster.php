<?php
include_once 'SQLTable.php';
/*
CREATE TABLE paymentmaster
(
  customerid character varying(15),
  paymentdate timestamp without time zone NOT NULL,
  paymentamount numeric(9,2) NOT NULL,
  paymenttype character varying(20) NOT NULL,
  paymentnote character varying(100) NOT NULL
)
*/
/*
customerid, paymentdate, paymentamount, paymenttype, paymentnote
*/

class psql_paymentmaster extends SQLTable{
	public $table_name = 'paymentmaster';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	private $deleteStatement;
	function psql_paymentmaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(customerid, paymentdate, paymentamount, 
										paymenttype, paymentnote)
			VALUES ($1,$2,$3,$4,$5);
HEREDOC;
		$this->selectStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE customerid = $1;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE FROM paymentmaster WHERE customerid = $1 AND rowid = $2
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertpaymentmaster", $this->insertStatement);
		pg_prepare($this->db, "selectpaymentmaster", $this->selectStatement);
		pg_prepare($this->db, "deletepaymentmaster", $this->deleteStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*customerid, paymentdate, paymentamount,paymenttype, paymentnote*/
		$customerid = '';
		$paymentdate = '';
		$paymentamount = '';
		$paymenttype = '';
		$paymentnote = '';
		
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['paymentdate'])){
			$paymentdate = $row['paymentdate'];
		}
		if(isset($row['paymentamount'])){
			$paymentamount = $row['paymentamount'];
		}
		if(isset($row['paymenttype'])){
			$paymenttype = $row['paymenttype'];
		}
		if(isset($row['paymentnote'])){
			$paymentnote = $row['paymentnote'];
		}
		
		$insertParams = array($customerid, $paymentdate, $paymentamount,$paymenttype, $paymentnote);
		
		$result = pg_execute($this->db, "insertpaymentmaster", $insertParams);
		if($result){
			return true;
		}
		return $result;
	}
	function Delete($row){
		$customerid = $row['customerid'];
		$rowid = $row['rowid'];
		$execute_result = pg_execute($this->db, "deletepaymentmaster", array($customerid, $rowid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
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
		$execute_result = pg_execute($this->db, "selectpaymentmaster", array($customerid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	
	
	function GetTitles(){
		throw new Exception('This function is not implemented yet');
	}
	function GetRowView($row){
		throw new Exception('This function is not implemented yet');
	}
}
?>