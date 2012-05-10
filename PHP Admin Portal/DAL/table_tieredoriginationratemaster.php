<?php
include 'SQLTable.php';
/*
CREATE TABLE tieredoriginationratemaster
(
  customerid character varying(15) NOT NULL,
  tier smallint NOT NULL,
  effectivedate date NOT NULL,
  retailrate numeric(9,7) NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT tieredoriginationratemaster_pkey PRIMARY KEY (customerid , tier , effectivedate ),
  CONSTRAINT tieredoriginationratemaster_rowid_key UNIQUE (rowid )
)
*/


class psql_tieredoriginationratemaster extends SQLTable{
	public $table_name = 'tieredoriginationratemaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $checkExistsStatement;
	
	function psql_tieredoriginationratemaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement  = <<< HEREDOC
			INSERT INTO {$this->table_name}("customerid","effectivedate","tier","retailrate")
						VALUES ($1,$2,$3,$4);
HEREDOC;
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "customerid" = $1 
					AND "effectivedate" = $2 
					AND "tier" = $3;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
						WHERE "customerid" = $1 
							AND "effectivedate" = $2 
							AND "tier" = $3;
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
		/*expected values : ($customerid, $effectivedate,$tier, $retailrate)*/
		#To do : validate input first.
		
		$insertParams = array($row['customerid'],$row['effectivedate'],$row['tier'],$row['retailrate']);
		$result = pg_execute($this->db, "insert", $insertParams);
		if($result){
			$this->rowsAdded++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		/*$customerid, $effectivedate,$tier*/
		
		$deleteParams = array($row['customerid'],$row['effectivedate'],$row['tier']);
		$result = pg_execute($this->db, "delete", $deleteParams);
		if($result){
			$this->rowsDeleted++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		/*$customerid, $effectivedate,$tier*/
		$selectParams = array($row['customerid'],$row['effectivedate'],$row['tier']);
		$result = pg_execute($this->db, "check", $selectParams);
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	}
	function Update($old, $new){
		if($this->DoesExist($old)){
			if($this->Delete($old)){
				return $this->Insert($new);
			}
		}
		else{
			return $this->Insert($new);
		}
	}
	
	function CountRows($customerid){	
		$queryNumberofRows = 'SELECT count(*) FROM '. $this->table_name.' WHERE "customerid" = \''.$customerid.'\';';
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		return $numberOfRows = $numberOfRowsArray[0];
	}
	
	function LimitedQuery($customerid, $limit, $offset){
		$fullQuery = "SELECT effectivedate,tier,retailrate FROM " . $this->table_name 
			. " WHERE customerid = '" . $customerid . "'"
			. " ORDER BY effectivedate,tier";
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
}
?>