
<?php
include_once 'SQLTable.php';
/*
CREATE TABLE webportalaccesstokens
(
  token character varying(100) NOT NULL,
  customerid character varying(15) NOT NULL,
  expires timestamp without time zone NOT NULL
)
*/
/*
  token,customerid,expires
*/
class psql_webportalaccesstokens extends SQLTable{
	public $table_name = 'webportalaccesstokens';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	function psql_webportalaccesstokens($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(token,customerid,expires)
			VALUES ($1,$2,$3);
HEREDOC;
		$this->selectStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE token = $1;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertwebportalaccesstokens", $this->insertStatement);
		pg_prepare($this->db, "selectwebportalaccesstokens", $this->selectStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*token,customerid,expires*/
		$customerid = '';
		$customername = '';
		$lrndiprate = '';
		
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['customername'])){
			$customername = $row['customername'];
		}
		if(isset($row['lrndiprate'])){
			$lrndiprate = $row['lrndiprate'];
		}
		
		
		$insertParams = array($token,$customerid,$expires);
		
		$result = pg_execute($this->db, "insertwebportalaccesstokens", $insertParams);
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
		$execute_result = pg_execute($this->db, "selectwebportalaccesstokens", array($token));
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
