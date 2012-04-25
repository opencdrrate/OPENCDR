<?php
include_once 'SQLTable.php';

class psql_didmaster extends SQLTable{
	public $table_name = 'didmaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $checkStatement;
	private $updateStatement;
	
	function psql_didmaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = 
			"INSERT INTO {$this->table_name} (did, customerid) VALUES ($1, $2)";
		$this->checkStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} WHERE did = $1 AND customerid = $2
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE from {$this->table_name} where rowid = $1
HEREDOC;
		$this->updateStatement = <<< HEREDOC
		update {$this->table_name} set customerid = $1 where did = $2;
HEREDOC;
	}
	private function E164DID($did){
		$newDID = $did;
		if(strlen($did) == 10 and substr($did,0,1) != '+'){
			$newDID = '+1'.$did;
		}
		if(strlen($did) == 11 and substr($did,0,1) == '1'){
			$newDID = '+1'.$did;
		}/*
		if(substr($did,0,3) == '011'){
			$did = '+'.substr($did,3,20);
		}*/
		
		return $newDID;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insert", $this->insertStatement);
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);
		pg_prepare($this->db, "update", $this->updateStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
		$did = '';
		if(!isset($row['did']) || empty($row['did'])){
			throw new Exception('did is a required field.');
		}
		else{
			$did = $this->E164DID($row['did']);
		}
		if(!isset($row['customerid']) || empty($row['customerid'])){
			throw new Exception('customerid is a required field');
		}
		$insertParams = array($did, $row['customerid']);
		if($this->DoesExist($row)){
			throw new Exception('did already exists for customer : ' . $row['customerid']);
			return false;
		}
		$result = pg_execute($this->db, "insert", $insertParams);
		if($result){
			return true;
		}
		else{
			return false;
		}
	}
	function Delete($row){
		$rowid = $row['rowid'];
		$deleteParams = array($rowid);
		$result = pg_execute($this->db, "delete", $deleteParams);
		if($result){
			return true;
		}
		return false;
	}
	function DoesExist($row){
		$did = $this->E164DID($row['did']);
		$result = pg_execute($this->db, "check", array($did, $row['customerid']));
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	
	}
	function Update($old, $new){
		$newCustomerID = $new['customerid'];
		$did = $old['did'];
		$e164did = $this->E164DID($did);
		$updateParams = array($newCustomerID, $e164did);
		$result = pg_execute($this->db, "update", $updateParams);
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
}
?>