

<?php
include_once 'SQLTable.php';

class psql_ipaddressmaster extends SQLTable{
	public $table_name = 'ipaddressmaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $checkStatement;
	private $updateStatement;
	
	function psql_ipaddressmaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = 
			"INSERT INTO {$this->table_name} (ipaddress, customerid) VALUES ($1, $2)";
		$this->checkStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} WHERE ipaddress = $1
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE from {$this->table_name} where rowid = $1
HEREDOC;
		$this->updateStatement = <<< HEREDOC
		update {$this->table_name} set customerid = $1 where ipaddress = $2;
HEREDOC;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, 'insertIPAddressMaster', $this->insertStatement);
		pg_prepare($this->db, 'checkIPAddressMaster', $this->checkStatement);
		pg_prepare($this->db, 'deleteIPAddressMaster', $this->deleteStatement);
		pg_prepare($this->db, 'updateIPAddressMaster', $this->updateStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
		$ipaddress = '';
		if(!isset($row['ipaddress']) || empty($row['ipaddress'])){
			throw new Exception('ipaddress is a required field.');
		}
		else{
			$ipaddress = $row['ipaddress'];
		}
		if(!isset($row['customerid']) || empty($row['customerid'])){
			throw new Exception('customerid is a required field');
		}
		$insertParams = array($ipaddress, $row['customerid']);
		if($this->DoesExist($row)){
			throw new Exception('ipaddress already exists for customer : ' . $row['customerid']);
			return false;
		}
		$result = pg_execute($this->db, 'insertIPAddressMaster', $insertParams);
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
		$result = pg_execute($this->db, 'deleteIPAddressMaster', $deleteParams);
		if($result){
			return true;
		}
		return false;
	}
	function DoesExist($row){
		$ipaddress = $row['ipaddress'];
		$result = pg_execute($this->db, 'checkIPAddressMaster', array($ipaddress));
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
		$ipaddress = $old['ipaddress'];
		$updateParams = array($newCustomerID, $ipaddress);
		$result = pg_execute($this->db, 'updateIPAddressMaster', $updateParams);
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
}
?>