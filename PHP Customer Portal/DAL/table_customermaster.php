
<?php
include_once 'SQLTable.php';
/*
CREATE TABLE customermaster
(
  customerid character varying(15) NOT NULL,
  customername character varying(100) NOT NULL,
  lrndiprate numeric(9,9) NOT NULL DEFAULT 0,
  cnamdiprate numeric(9,9) NOT NULL DEFAULT 0,
  indeterminatejurisdictioncalltype smallint,
  billingcycle character varying(15),
  rowid serial NOT NULL,
  )
*/
/*
  customerid,customername,lrndiprate,cnamdiprate,indeterminatejurisdictioncalltype,billingcycle
*/
class psql_customermaster extends SQLTable{
	public $table_name = 'customermaster';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	function psql_customermaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(customerid,customername,lrndiprate,
						cnamdiprate,indeterminatejurisdictioncalltype,billingcycle)
			VALUES ($1,$2,$3,$4,$5,$6);
HEREDOC;
		$this->selectStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE customerid = $1;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertcustomermaster", $this->insertStatement);
		pg_prepare($this->db, "selectcustomermaster", $this->selectStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*customerid,customername,lrndiprate,cnamdiprate,
		indeterminatejurisdictioncalltype,billingcycle*/
		$customerid = '';
		$customername = '';
		$lrndiprate = '';
		$cnamdiprate = '';
		$indeterminatejurisdictioncalltype = '';
		$billingcycle = '';
		
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['customername'])){
			$customername = $row['customername'];
		}
		if(isset($row['lrndiprate'])){
			$lrndiprate = $row['lrndiprate'];
		}
		if(isset($row['cnamdiprate'])){
			$cnamdiprate = $row['cnamdiprate'];
		}
		if(isset($row['indeterminatejurisdictioncalltype'])){
			$indeterminatejurisdictioncalltype = $row['indeterminatejurisdictioncalltype'];
		}
		if(isset($row['billingcycle'])){
			$billingcycle = $row['billingcycle'];
		}
		
		
		$insertParams = array($customerid,$customername,$lrndiprate,$cnamdiprate,
			$indeterminatejurisdictioncalltype,$billingcycle);
		
		$result = pg_execute($this->db, "insertcustomermaster", $insertParams);
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
		$execute_result = pg_execute($this->db, "selectcustomermaster", array($customerid));
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