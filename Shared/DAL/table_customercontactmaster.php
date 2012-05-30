<?php
/*
CREATE TABLE customercontactmaster
(
  customerid character varying(15) NOT NULL,
  primaryemailaddress character varying(100) NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT customercontactmaster_pkey PRIMARY KEY (customerid ),
  CONSTRAINT customercontactmaster_rowid_key UNIQUE (rowid )
)
*/
include_once 'SQLTable.php';

class psql_customercontactmaster extends SQLTable{
	private $connectString;
	private $db;
	public $table_name = 'customercontactmaster';
	
	private $insertStatement;
	private $checkExistsStatement;
	private $deleteStatement;
	function psql_customercontactmaster($connectString){
		$this->connectString = $connectString;
		
		$this->insertStatement = <<< HEREDOC
	INSERT INTO {$this->table_name} (customerid,primaryemailaddress) 
	VALUES ($1,$2)
HEREDOC;
		
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "customerid" = $1
HEREDOC;

		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
				WHERE "customerid" = $1
HEREDOC;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "insert_customercontact", $this->insertStatement);
		pg_prepare($this->db, "check_customercontact", $this->checkExistsStatement);
		pg_prepare($this->db, "delete_customercontact", $this->deleteStatement);
		
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	$customerid = $row['customerid'];
	$primaryemailaddress = $row['primaryemailaddress'];
	$insertParams = array($customerid, $primaryemailaddress);
		
		if($this->DoesExist(array('customerid' => $customerid) ) ){
			return false;
		}
		
		$result = pg_execute($this->db, "insert_customercontact", $insertParams);
		if($result){
			$this->rowsAdded++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		$selectParams = array($row['customerid']);
		$result = pg_execute($this->db, "delete_customercontact", $selectParams);
		if($result){
			$this->rowsDeleted++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		$selectParams = array($row['customerid']);
		$result = pg_execute($this->db, "check_customercontact", $selectParams);
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
	
	
	function GetTitles(){
		throw new Exception('This function is not implemented yet');
	}
	function GetRowView($row){
		throw new Exception('This function is not implemented yet');
	}
	
}
?>