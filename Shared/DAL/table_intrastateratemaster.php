<?php
include_once 'SQLTable.php';
/*
CREATE TABLE intrastateratemaster
(
  customerid character varying(15) NOT NULL,
  effectivedate date NOT NULL,
  npanxxx character(7) NOT NULL,
  retailrate numeric(9,7) NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT intrastateratemaster_pkey PRIMARY KEY (customerid , effectivedate , npanxxx ),
  CONSTRAINT intrastateratemaster_rowid_key UNIQUE (rowid )
)
*/

class psql_intrastateratemaster extends SQLTable{
	public $table_name = 'intrastateratemaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $checkExistsStatement;
	
	function psql_intrastateratemaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement  = <<< HEREDOC
			INSERT INTO {$this->table_name}("customerid","effectivedate","npanxxx","retailrate")
						VALUES ($1,$2,$3,$4);
HEREDOC;
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "customerid" = $1 
					AND "effectivedate" = $2 
					AND "npanxxx" = $3;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
						WHERE "customerid" = $1 
							AND "effectivedate" = $2 
							AND "npanxxx" = $3;
HEREDOC;
		#to do : check connection?
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "insert", $this->insertStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);
		pg_prepare($this->db, "check", $this->checkExistsStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	function Insert($row){
		/*expected values : ($customerid, $effectivedate,$npanxxx, $retailrate)*/
		#To do : validate input first.
		
		$insertParams = array($row['customerid'],$row['effectivedate'],$row['npanxxx'],$row['retailrate']);
		$result = pg_execute($this->db, "insert", $insertParams);
		if($result){
			$this->rowsAdded++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		/*$customerid, $effectivedate,$npanxxx*/
		
		$deleteParams = array($row['customerid'],$row['effectivedate'],$row['npanxxx']);
		$result = pg_execute($this->db, "delete", $deleteParams);
		if($result){
			$this->rowsDeleted++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		/*$customerid, $effectivedate,$npanxxx*/
		$selectParams = array($row['customerid'],$row['effectivedate'],$row['npanxxx']);
		$result = pg_execute($this->db, "check", $selectParams);
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
		$fullQuery = "SELECT effectivedate,npanxxx,retailrate FROM " . $this->table_name 
			. " WHERE customerid = '" . $customerid . "'"
			. " ORDER BY effectivedate,npanxxx";
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