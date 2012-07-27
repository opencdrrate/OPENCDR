<?php
include_once 'SQLTable.php';
/*
CREATE TABLE callrecordmaster
(
  callid character varying(100) NOT NULL,
  customerid character varying(15) NOT NULL,
  calltype smallint,
  calldatetime timestamp without time zone NOT NULL,
  duration integer NOT NULL,
  billedduration integer NOT NULL,
  direction character(1),
  sourceip character varying(15),
  originatingnumber character varying(50) NOT NULL,
  destinationnumber character varying(50) NOT NULL,
  lrn character varying(50),
  lrndipfee numeric(9,9) NOT NULL DEFAULT 0,
  billednumber character varying(50) NOT NULL,
  billedprefix character varying(10),
  rateddatetime timestamp without time zone NOT NULL,
  retailrate numeric(9,7) NOT NULL,
  cnamdipped boolean,
  cnamfee numeric(9,9) NOT NULL DEFAULT 0,
  billedtier smallint,
  ratecenter character varying(50),
  billingbatchid character varying(15),
  retailprice numeric(19,7) NOT NULL,
  carrierid character varying(100),
  wholesalerate numeric(19,7),
  wholesaleprice numeric(19,7)
)
*/
/*
callid, calltype, calldatetime, billedduration, 
       originatingnumber, destinationnumber,  
       lrndipfee, retailrate,cnamfee, retailprice
*/

class psql_callrecordmaster extends SQLTable{
	public $table_name = 'callrecordmaster';
	private $db;
	private $connectString;
	
	private $selectStatement;
	private $selectDateStatement;
	
	function psql_callrecordmaster($connectString){
		$this->connectString = $connectString;
		$this->selectStatement = <<< HEREDOC
		SELECT callid, calltype, calldatetime, billedduration, 
       originatingnumber, destinationnumber,  
       lrndipfee, retailrate,cnamfee, retailprice FROM {$this->table_name} WHERE customerid = $1;
HEREDOC;
		$this->selectDateStatement = <<< HEREDOC
	SELECT callid, calltype, calldatetime, billedduration, 
       originatingnumber, destinationnumber,  
       lrndipfee, retailrate,cnamfee, retailprice FROM {$this->table_name} 
	   WHERE customerid = $1 and calldatetime > $2 and calldatetime < $3;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		//pg_prepare($this->db, "insertcallrecordmaster", $this->insertStatement);
		pg_prepare($this->db, "select_callrecordmaster", $this->selectStatement);
		pg_prepare($this->db, "selectdate_callrecordmaster", $this->selectDateStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
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
		throw new Exception('This function is not implemented yet');
	}
	
	function Select($customerid){
		$execute_result = pg_execute($this->db, "select_callrecordmaster", array($customerid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	function SelectDate($customerid, $startdate,$enddate){
		$execute_result = pg_execute($this->db, "selectdate_callrecordmaster", array($customerid, $startdate, $enddate));
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