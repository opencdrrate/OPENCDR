<?php
include_once 'SQLTable.php';
/*
CREATE TABLE simpleterminationratemaster
(
  customerid character varying(15) NOT NULL,
  billedprefix character varying(10) NOT NULL,
  effectivedate timestamp without time zone NOT NULL,
  retailrate numeric(9,7) NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT simpleterminationratemaster_pkey PRIMARY KEY (customerid , billedprefix , effectivedate ),
  CONSTRAINT simpleterminationratemaster_rowid_key UNIQUE (rowid )
)
*/


class psql_simpleterminationratemaster extends SQLTable{
	public $table_name = 'simpleterminationratemaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $checkExistsStatement;
	
	function psql_simpleterminationratemaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement  = <<< HEREDOC
			INSERT INTO {$this->table_name}("customerid","effectivedate","billedprefix","retailrate")
				VALUES ($1,$2,$3,$4);
HEREDOC;
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "customerid" = $1 
					AND "effectivedate" = $2 
					AND "billedprefix" = $3;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
				WHERE "customerid" = $1 
					AND "effectivedate" = $2 
					AND "billedprefix" = $3;
HEREDOC;
		#to do : check connection?
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "simpletermination_insert", $this->insertStatement);
		pg_prepare($this->db, "simpletermination_delete", $this->deleteStatement);
		pg_prepare($this->db, "simpletermination_check", $this->checkExistsStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	private function E164Format($billedprefix){
	
		if(substr($billedprefix, 0,1) != '+' and substr($billedprefix, 0,1) != '0'){
			$billedprefix = '+' . $billedprefix;
		}
		return $billedprefix;
	}
	function Insert($row){
		/*expected values : ($customerid, $effectivedate,$billedprefix, $retailrate)*/
		#To do : validate input first.
		$billedprefix = $this->E164Format($row['billedprefix']);
		
		$insertParams = array($row['customerid'],$row['effectivedate'],$billedprefix,$row['retailrate']);
		$result = pg_execute($this->db, "simpletermination_insert", $insertParams);
		if($result){
			$this->rowsAdded++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		$billedprefix = $this->E164Format($row['billedprefix']);
		
		/*$customerid, $effectivedate,$billedprefix*/
		
		$deleteParams = array($row['customerid'],$row['effectivedate'],$billedprefix);
		$result = pg_execute($this->db, "simpletermination_delete", $deleteParams);
		if($result){
			$this->rowsDeleted++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		/*$customerid, $effectivedate,$billedprefix*/
		$billedprefix = $this->E164Format($row['billedprefix']);
		
		$selectParams = array($row['customerid'],$row['effectivedate'],$billedprefix);
		$result = pg_execute($this->db, "simpletermination_check", $selectParams);
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	}
	
	function CountRows($customerid){	
		$queryNumberofRows = 'SELECT count(*) FROM '. $this->table_name.' WHERE "customerid" = \''.$customerid.'\';';
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		return $numberOfRows = $numberOfRowsArray[0];
	}
	
	function LimitedQuery($customerid, $limit, $offset){
		$fullQuery = "SELECT effectivedate,billedprefix,retailrate FROM " . $this->table_name 
			. " WHERE customerid = '" . $customerid . "'"
			. " ORDER BY effectivedate,billedprefix";
		$limitedQuery = $fullQuery
			. " LIMIT "
			. $limit
			. " OFFSET "
			. $offset	
			. ";";

		$limitedQueryResult = pg_query($this->db, $limitedQuery);
		$assocArray = array();
		while($row = pg_fetch_assoc($limitedQueryResult)){
			$assocArray[] = $row;
		}
		
		return $assocArray;
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