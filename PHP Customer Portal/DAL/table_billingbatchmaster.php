<?php
/*
CREATE TABLE billingbatchmaster
(
  billingbatchid character varying(15) NOT NULL,
  billingdate date NOT NULL,
  duedate date NOT NULL,
  billingcycleid character varying(15) NOT NULL,
  usageperiodend date NOT NULL,
  rowid serial NOT NULL,
  CONSTRAINT billingbatchmaster_pkey PRIMARY KEY (billingbatchid ),
  CONSTRAINT billingbatchmaster_rowid_key UNIQUE (rowid )
)
*/
/*
billingbatchid, billingdate, duedate, billingcycleid, usageperiodend
*/

class psql_billingbatchmaster extends SQLTable{
	public $table_name = 'billingbatchmaster';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $selectStatement;
	function psql_billingbatchmaster($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(billingbatchid, billingdate, duedate, billingcycleid, usageperiodend)
			VALUES ($1,$2,$3,$4,$5);
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
		pg_prepare($this->db, "insertbillingbatchmaster", $this->insertStatement);
		pg_prepare($this->db, "selectbillingbatchmaster", $this->selectStatement);
		/*
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);*/
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
	/*billingbatchid, customerid, calltype, lineitemtype, lineitemdesc, 
       lineitemamount, lineitemquantity, periodstartdate, periodenddate*/
		$billingbatchid = '';
		$customerid = '';
		$calltype = '';
		$lineitemtype = '';
		$lineitemdesc = '';
		$lineitemamount = '';
		$lineitemquantity = '';
		$periodstartdate = '';
		$periodenddate = '';
		
		if(isset($row['billingbatchid'])){
			$billingbatchid = $row['billingbatchid'];
		}
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['calltype'])){
			$calltype = $row['calltype'];
		}
		if(isset($row['lineitemtype'])){
			$lineitemtype = $row['lineitemtype'];
		}
		if(isset($row['lineitemdesc'])){
			$lineitemdesc = $row['lineitemdesc'];
		}
		if(isset($row['lineitemamount'])){
			$lineitemamount = $row['lineitemamount'];
		}
		if(isset($row['lineitemquantity'])){
			$lineitemquantity = $row['lineitemquantity'];
		}
		if(isset($row['periodstartdate'])){
			$periodstartdate = $row['periodstartdate'];
		}
		if(isset($row['periodenddate'])){
			$periodenddate = $row['periodenddate'];
		}
		
		
		$insertParams = array($billingbatchid,$customerid,$calltype,$lineitemtype,
		$lineitemdesc,$lineitemamount,$lineitemquantity,$periodstartdate,$periodenddate);
		
		$result = pg_execute($this->db, "insertbillingbatchmaster", $insertParams);
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
		$execute_result = pg_execute($this->db, "selectbillingbatchmaster", array($customerid));
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