<?php
include_once 'SQLTable.php';
/*callrecordmaster_held*/
/*
CREATE TABLE callrecordmaster_held
(
  callid character varying(100) NOT NULL,
  customerid character varying(15),
  calltype smallint,
  calldatetime timestamp without time zone NOT NULL,
  duration integer NOT NULL,
  direction character(1),
  sourceip character varying(15),
  originatingnumber character varying(50) NOT NULL,
  destinationnumber character varying(50) NOT NULL,
  lrn character varying(50),
  cnamdipped boolean,
  ratecenter character varying(50),
  carrierid character varying(100),
  wholesalerate numeric(19,7),
  wholesaleprice numeric(19,7),
  errormessage character varying(100) NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT callrecordmaster_held_pkey PRIMARY KEY (callid ),
  CONSTRAINT callrecordmaster_held_rowid_key UNIQUE (rowid )
)
*/
class psql_callrecordmaster_held extends SQLTable{
	public $table_name = <<< HEREDOC
	callrecordmaster_held
HEREDOC;
	public $IsConnected = false;
	private $connectString;
	private $db;
	
	private $selectStatement;
	function psql_callrecordmaster_held($connectString){
		$this->connectString = $connectString;
		$this->selectStatement = <<< HEREDOC
		SELECT callid, customerid, calltype, calldatetime, duration, direction, 
       sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
       ratecenter, carrierid, wholesalerate, wholesaleprice, errormessage, 
       rowid
  FROM {$this->table_name};
HEREDOC;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		pg_prepare($this->db, "selectAll_callrecordmaster_held", $this->selectStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
		throw new Exception('This function is not implemented yet');
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
		$result = pg_execute($this->db, "selectAll_callrecordmaster_held", array());
		
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	
	function SelectSubset($offset = 0, $limit = 0){
		$query = <<< HEREDOC
		SELECT * FROM {$this->table_name}
HEREDOC;
		
		$limitedQuery = $query;
		/*if($limit > 0){
			$limitedQuery .= " LIMIT "
				. $limit
				. " OFFSET "
				. $offset	
				. ";";
		}*/
		$queryResults = pg_query($this->db, $limitedQuery);
		$allArrayResults = array();
		while($row = pg_fetch_assoc($queryResults)){
			$allArrayResults[] = $row;
		}
		return $allArrayResults;
	}
	
	function CountResults(){
		$queryNumberofRows = <<< HEREDOC
		SELECT count(*) FROM {$this->table_name}
HEREDOC;
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		$numberOfRows = $numberOfRowsArray[0];
		return $numberOfRows;
	}
	
	function GetTitles(){
		throw new Exception('This function is not implemented yet');
	}
	function GetRowView($row){
		throw new Exception('This function is not implemented yet');
	}
}
?>