<?php
include_once 'SQLTable.php';
/*CREATE TABLE customermaster
(
  customerid character varying(15) NOT NULL,
  customername character varying(100) NOT NULL,
  lrndiprate numeric(9,9) NOT NULL DEFAULT 0,
  cnamdiprate numeric(9,9) NOT NULL DEFAULT 0,
  indeterminatejurisdictioncalltype smallint,
  billingcycle character varying(15),
  rowid serial NOT NULL,
  CONSTRAINT customermaster_pkey PRIMARY KEY (customerid ),
  CONSTRAINT customermaster_rowid_key UNIQUE (rowid ),
  CONSTRAINT customermaster_indeterminatejurisdictioncalltype_check CHECK (indeterminatejurisdictioncalltype = 5 OR indeterminatejurisdictioncalltype = 10)
)*/

class psql_customermaster extends SQLTable{

	public $table_name = 'customermaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $selectStatement;
	private $checkStatement;
	
	function psql_customermaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name} 
		(customerid, customername, lrndiprate, cnamdiprate, indeterminatejurisdictioncalltype, 
		billingcycle, customertype) 
		VALUES ($1,$2,$3,$4,$5,$6,$7)
HEREDOC;
		$this->checkStatement = <<< HEREDOC
		SELECT 1 FROM {$this->table_name} WHERE customerid = $1
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE FROM {$this->table_name} WHERE customerid = $1
HEREDOC;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insert_customermaster", $this->insertStatement);
		pg_prepare($this->db, "check_customermaster", $this->checkStatement);
		pg_prepare($this->db, "delete_customermaster", $this->deleteStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}

	function Insert($row){
	/*(customerid, customername, lrndiprate, 
	cnamdiprate, indeterminatejurisdictioncalltype, billingcycle) */
	$customerid = $row['customerid']; 
	$customername = $row['customername'];
	$lrndiprate = $row['lrndiprate'];
	$cnamdiprate = $row['cnamdiprate'];
	$indeterminatejurisdictioncalltype = $row['indeterminatejurisdictioncalltype'];
	$billingcycle = $row['billingcycle'];
	$customertype = $row['customertype'];
	
		$insertParams = array($customerid, $customername,$lrndiprate,
								$cnamdiprate, $indeterminatejurisdictioncalltype,$billingcycle,$customertype);
		
		if($this->DoesExist(array('customerid' => $customerid)) ){
			return false;
		}
		
		$result = pg_execute($this->db, "insert_customermaster", $insertParams);
		if($result){
			$this->rowsAdded++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		$deleteParams = array($row['customerid']);
		$result = pg_execute($this->db, "delete_customermaster", $deleteParams);
		if($result){
			$this->rowsDeleted++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		$selectParams = array($row['customerid']);
		$result = pg_execute($this->db, "check_customermaster", $selectParams);
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	}
	function SelectAll(){
		$selectQuery = <<< HEREDOC
			SELECT customerid, customername, lrndiprate, cnamdiprate, 
			indeterminatejurisdictioncalltype, billingcycle
			FROM {$this->table_name}
HEREDOC;
		$result = pg_query($this->db, $selectQuery);
		if (!$result) {
			echo pg_last_error();
			exit();
		}
		$out = array();
		while($myrow = pg_fetch_assoc($result)) { 
			$out[] = $myrow;
		}
		return $out;
	}
	
	function Select($customerid){
		$selectQuery = <<< HEREDOC
			SELECT customerid, customername, lrndiprate, cnamdiprate, 
			indeterminatejurisdictioncalltype, billingcycle
			FROM {$this->table_name} WHERE customerid = '{$customerid}'
HEREDOC;
		$result = pg_query($this->db, $selectQuery);
		if($myrow = pg_fetch_assoc($result)){
			return $myrow;
		}
		else{
			return false;
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