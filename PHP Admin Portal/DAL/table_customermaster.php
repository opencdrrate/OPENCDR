<?php
include 'SQLTable.php';
/*CREATE TABLE customermaster
(
  customerid character varying(15) NOT NULL,
  customername character varying(100) NOT NULL,
  lrndiprate numeric(9,9) NOT NULL DEFAULT 0,
  cnamdiprate numeric(9,9) NOT NULL DEFAULT 0,
  indeterminatejurisdictioncalltype smallint,
  billingcycle character varying(15),
  rowid serial NOT NULL,
  CONSTRAINT customermaster_pkey PRIMARY KEY (customerid ),
  CONSTRAINT customermaster_rowid_key UNIQUE (rowid ),
  CONSTRAINT customermaster_indeterminatejurisdictioncalltype_check CHECK (indeterminatejurisdictioncalltype = 5 OR indeterminatejurisdictioncalltype = 10)
)*/

class psql_customermaster extends SQLTable{

	public $table_name = 'didmaster';
	private $connectString = '';
	private $db = null;
	private $insertStatement;
	private $deleteStatement;
	private $selectStatement;
	
	function psql_customermaster($connectString){
		$this->connectString = $connectString;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
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
		$selectQuery = <<< HEREDOC
			SELECT customerid, customername, lrndiprate, cnamdiprate, 
			indeterminatejurisdictioncalltype, billingcycle
			FROM customermaster
HEREDOC;
		$result = pg_query($this->db, $selectQuery);
		if (!$result) {
			echo pg_last_error();
			exit();
		}
		$out = array();
		while($myrow = pg_fetch_assoc($result)) { 
			$out[] = $myrow;
		}
		return $out;
	}
	
}
?>