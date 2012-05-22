<?php
include_once 'SQLTable.php';
include_once 'table_callrecordmaster_tbr.php';
/*
CREATE TABLE bandwidthcdr
(
  debtor_id character varying(15) NOT NULL,
  itemid character varying(100) NOT NULL,
  billable_minutes numeric(19,6) NOT NULL,
  itemtype character varying(100) NOT NULL,
  trans_rate numeric(19,7),
  amount numeric(19,7),
  record_date timestamp without time zone NOT NULL,
  src character varying(50) NOT NULL,
  dest character varying(50) NOT NULL,
  dst_rcs character varying(50),
  lrn character varying(50),
  customerid character varying(15),
  direction character(1),
  calltype smallint,
  rawduration integer
)
*/
/*debtor_id, itemid, billable_minutes, itemtype, trans_rate, amount, 
       record_date, src, dest, dst_rcs, lrn, customerid, direction, 
       calltype, rawduration*/
	   /*
	   
$bandwidthMap['DEBTOR_ID'] = 'debtor_id';
$bandwidthMap['item.id'] = 'itemid';
$bandwidthMap['computed.billable_minutes'] = 'billable_minutes';
$bandwidthMap['item.type'] = 'itemtype';
$bandwidthMap['computed.trans_rate'] = 'trans_rate';
$bandwidthMap['AMOUNT'] = 'amount';
$bandwidthMap['RECORD_DATE'] = 'record_date';
$bandwidthMap['item.src'] = 'src';
$bandwidthMap['item.dest'] = 'dest';
$bandwidthMap['item.dst_rcs'] = 'dst_rcs';
$bandwidthMap['lrn'] = 'lrn';

	   */
class psql_bandwidthcdr extends SQLTable{
	private $table_name = 'bandwidthcdr';
	private $connectString;
	
	private $insertStatement;
	private $checkExistsStatement;
	private $deleteStatement;
	
	public $SkippedDuplicateCount = 0;
	public $InsertedCount = 0;
	function psql_bandwidthcdr($connectString){
		$this->connectString = $connectString;
		
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(debtor_id, itemid, billable_minutes, itemtype, 
		trans_rate, amount,record_date, src, dest, dst_rcs, lrn, customerid, direction, 
       calltype, rawduration)
	   VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15);
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
				WHERE "itemid" = $1
HEREDOC;
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "itemid" = $1
HEREDOC;
	}
	
	private $callrecordmaster_table;
	function Connect(){
		$this->callrecordmaster_table = new psql_callrecordmaster_tbr($this->connectString);
		$this->callrecordmaster_table->Connect();
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "insertbandwidth", $this->insertStatement);
		pg_prepare($this->db, "deletebandwidth", $this->deleteStatement);
		pg_prepare($this->db, "checkbandwidth", $this->checkExistsStatement);
	}
	function Disconnect(){
		pg_close($this->db);
		$this->callrecordmaster_table->Disconnect();
	}
/*debtor_id, itemid, billable_minutes, itemtype, trans_rate, amount, 
       record_date, src, dest, dst_rcs, lrn, customerid, direction, 
       calltype, rawduration*/

	function Insert($row){
		$debtor_id = null;
		if(isset($row['debtor_id'])){
			$debtor_id = $row['debtor_id'];
		}
		$itemid =null;
		if(isset($row['itemid'])){
			$itemid = $row['itemid'];
		}
		$billable_minutes = null;
		if(isset($row['billable_minutes'])){
			$billable_minutes = $row['billable_minutes'];
		}
		$itemtype = null;
		if(isset($row['itemtype'])){
			$itemtype = $row['itemtype'];
		}
		$trans_rate = null;
		if(isset($row['trans_rate'])){
			$trans_rate = $row['trans_rate'];
		}
		$amount = null;
		if(isset($row['amount'])){
			$amount = $row['amount'];
		}
		$record_date = null;
		if(isset($row['record_date'])){
			$record_date = $row['record_date'];
		}
		$src = null;
		if(isset($row['src'])){
			$src = $row['src'];
		}
		$dest = null;
		if(isset($row['dest'])){
			$dest = $row['dest'];
		}
		$dst_rcs = null;
		if(isset($row['dst_rcs'])){
			$dst_rcs = $row['dst_rcs'];
		}
		$lrn = null;
		if(isset($row['lrn'])){
			$lrn = $row['lrn'];
		}
		$customerid = null;
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		$direction = null;
		if(isset($row['direction'])){
			$direction = $row['direction'];
		}
		$calltype = null;
		if(isset($row['calltype'])){
			$calltype = $row['calltype'];
		}
		$rawduration = null;
		if(isset($row['rawduration'])){
			$rawduration = $row['rawduration'];
		}
		
		$insertParams = array(  $debtor_id, $itemid, $billable_minutes, $itemtype, $trans_rate, $amount, 
       $record_date, $src, $dest, $dst_rcs, $lrn, $customerid, $direction, 
       $calltype, $rawduration );
		
		if($this->DoesExist(array('itemid' => $itemid)) 
				|| $this->callrecordmaster_table->DoesExist(array('callid'=>$itemid))){
			$this->SkippedDuplicateCount++;
			return false;
		}
		
		$result = pg_execute($this->db, "insertbandwidth", $insertParams);
		if($result){
			$this->InsertedCount++;
			return true;
		}
		return $result;
	}
	function Delete($row){
	
		$deleteParams = array($row['itemid']);
		$result = pg_execute($this->db, "deletebandwidth", $deleteParams);
		if($result){
			$this->DeletedCount++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		$selectParams = array($row['itemid']);
		$result = pg_execute($this->db, "checkbandwidth", $selectParams);
		$hasEntry = pg_fetch_array($result);
		if(!$hasEntry){
			return false;
		}
		else{
			return true;
		}
	}
	function Update($old, $new){
		throw new Exception('This function is not implemented yet');
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
	
	function MoveToTBR(){
		
		$moveStatement = 'SELECT "fnMoveBandwidthCDRToTBR"();';
		$deleteStatement = <<< HEREDOC
		DELETE FROM {$this->table_name} WHERE itemid
 in (select callid from {$this->callrecordmaster_table->table_name}) 
HEREDOC;
		pg_query($this->db, $deleteStatement);
		pg_query($this->db, $moveStatement);
	}
}

?>