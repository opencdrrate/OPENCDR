
<?php
include_once 'SQLTable.php';
/*
CREATE TABLE webportalaccess
(
  username character varying(100) NOT NULL,
  nonce character(10) NOT NULL,
  hashedpassword character varying(500),
  customerid character varying(15)
)
*/
/*
  username,nonce,hashedpassword,customerid
*/
class psql_webportalaccess extends SQLTable{
	public $table_name = 'webportalaccess';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	function psql_webportalaccess($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(username,nonce,hashedpassword,customerid)
			VALUES ($1,$2,$3,$4);
HEREDOC;
		$this->selectStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE username = $1;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertwebportalaccess", $this->insertStatement);
		pg_prepare($this->db, "selectwebportalaccess", $this->selectStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*username,nonce,hashedpassword,customerid*/
		$username = '';
		$nonce = '';
		$lrndiprate = '';
		
		if(isset($row['username'])){
			$username = $row['username'];
		}
		if(isset($row['nonce'])){
			$nonce = $row['nonce'];
		}
		if(isset($row['hashedpassword'])){
			$hashedpassword = $row['hashedpassword'];
		}
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		
		
		$insertParams = array($username,$nonce,$hashedpassword,$customerid);
		
		$result = pg_execute($this->db, "insertwebportalaccess", $insertParams);
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
	
	function Select($token){
		$execute_result = pg_execute($this->db, "selectwebportalaccess", array($token));
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
