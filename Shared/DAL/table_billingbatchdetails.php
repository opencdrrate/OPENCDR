<?php
include_once 'SQLTable.php';
/*
CREATE TABLE billingbatchdetails
(
  billingbatchid character varying(15) NOT NULL,
  customerid character varying(15) NOT NULL,
  calltype smallint NOT NULL,
  lineitemtype smallint NOT NULL,
  lineitemdesc character varying(100) NOT NULL,
  lineitemamount numeric(9,2) NOT NULL,
  lineitemquantity integer NOT NULL,
  periodstartdate date NOT NULL,
  periodenddate date NOT NULL
)
*/
/*
billingbatchid, customerid, calltype, lineitemtype, lineitemdesc, 
       lineitemamount, lineitemquantity, periodstartdate, periodenddate
*/

class psql_billingbatchdetails extends SQLTable{
	public $table_name = 'billingbatchdetails';
	private $db;
	private $connectString;
	
	private $insertStatement;
	private $updateStatement;
	private $deleteStatement;
	private $selectStatement;
	private $batchesStatement;
	private $selectByBatchId;
	
	function psql_billingbatchdetails($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(billingbatchid, customerid, calltype, 
									lineitemtype, lineitemdesc,lineitemamount, 
									lineitemquantity, periodstartdate, periodenddate)
			VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9);
HEREDOC;
		$this->selectCustomerBatchStatement = <<< HEREDOC
		SELECT * FROM {$this->table_name} WHERE customerid = $1 AND billingbatchid = $2;
HEREDOC;
		$this->batchesStatement = <<< HEREDOC
		SELECT billingbatchid,sum(lineitemamount) as totalamount, count(lineitemamount) as items,
			max(periodstartdate) as "periodstartdate", max(periodenddate) as "periodenddate"
		FROM {$this->table_name} where customerid = $1 group by billingbatchid, customerid;
HEREDOC;
		$this->updateStatement = <<< HEREDOC
		UPDATE {$this->table_name} 
			SET lineitemdesc=$1, lineitemamount=$2
			WHERE rowid = $3;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE from {$this->table_name}
			WHERE rowid = $1;
HEREDOC;
		$this->selectByBatchId = <<< HEREDOC
		SELECT customerid, sum(lineitemamount) as "amount",
			count(*) as "items", max(periodstartdate) as "periodstartdate", max(periodenddate) as "periodenddate"
			FROM billingbatchdetails WHERE billingbatchid = $1 group by customerid order by customerid;
HEREDOC;
	}
	function Connect(){
		$this->db = pg_connect($this->connectString);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insertbillingbatchdetails", $this->insertStatement);
		pg_prepare($this->db, "selectbatches", $this->batchesStatement);
		pg_prepare($this->db, "selectcustomerbatch", $this->selectCustomerBatchStatement);
		pg_prepare($this->db, "selectbybatchid", $this->selectByBatchId);
		
		pg_prepare($this->db, "updatebillingbatchdetails", $this->updateStatement);
		pg_prepare($this->db, "deletebillingbatchdetails", $this->deleteStatement);
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
		
		$result = pg_execute($this->db, "insertbillingbatchdetails", $insertParams);
		if($result){
			return true;
		}
		return $result;
	}
	function Delete($row){
		$rowid = $row['rowid'];
		$result = pg_execute($this->db, "deletebillingbatchdetails", array($rowid));
		if(empty($result) or !$result){
			return false;
		}
		else if($result){
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		throw new Exception('This function is not implemented yet');
	}
	function Update($old, $new){
		$rowid = $old['rowid'];
		$lineitemdesc = $new['lineitemdesc'];
		$lineitemamount = $new['lineitemamount'];
		
		$result = pg_execute($this->db, "updatebillingbatchdetails", array($lineitemdesc,$lineitemamount,$rowid));
		
		if(empty($result) or !$result){
			return false;
		}
		else if($result){
			return true;
		}
		return $result;
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
	
	function SelectCustomerBatch($customerid, $billingbatchid){
		$execute_result = pg_execute($this->db, "selectcustomerbatch", array($customerid,$billingbatchid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	
	function GetCustomers($batchid){
		$execute_result = pg_execute($this->db, "selectbybatchid", array($batchid));
		$result = pg_fetch_all($execute_result);
		if(!$result){
			return array();
		}
		else{
			return $result;
		}
	}
	
	function GetBatches($customerid){
		$execute_result = pg_execute($this->db, "selectbatches", array($customerid));
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