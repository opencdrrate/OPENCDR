<?php
include_once 'SQLTable.php';
/*
CREATE TABLE customerbillingaddressmaster
(
  customerid character varying(15) NOT NULL,
  address1 character varying(100) NOT NULL,
  address2 character varying(100) NOT NULL,
  city character varying(100) NOT NULL,
  stateorprov character varying(100) NOT NULL,
  country character varying(100) NOT NULL,
  zipcode character varying(15) NOT NULL
)
*/
class psql_customerbillingaddressmaster extends SQLTable{
	public $table_name = 'customerbillingaddressmaster';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	function psql_customerbillingaddressmaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(customerid,address1,address2,city,stateorprov,country,zipcode)
			VALUES ($1,$2,$3,$4,$5,$6,$7);
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
		pg_prepare($this->db, "insertbillingmaster", $this->insertStatement);
		pg_prepare($this->db, "selectbillingmaster", $this->selectStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*customerid,address1,address2,city,stateorprov,country,zipcode*/
		$customerid = '';
		$address1 = '';
		$address2 = '';
		$city = '';
		$stateorprov = '';
		$country = '';
		$zipcode = '';
		
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['address1'])){
			$address1 = $row['address1'];
		}
		if(isset($row['address2'])){
			$address2 = $row['address2'];
		}
		if(isset($row['city'])){
			$city = $row['city'];
		}
		if(isset($row['stateorprov'])){
			$stateorprov = $row['stateorprov'];
		}
		if(isset($row['country'])){
			$country = $row['country'];
		}
		if(isset($row['zipcode'])){
			$zipcode = $row['zipcode'];
		}
		
		
		$insertParams = array($customerid,$address1,$address2,
								$city,$stateorprov,$country,$zipcode);
		
		$result = pg_execute($this->db, "insertbillingmaster", $insertParams);
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
		$execute_result = pg_execute($this->db, "selectbillingmaster", array($customerid));
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