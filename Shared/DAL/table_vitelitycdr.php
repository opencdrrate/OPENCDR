<?php
include_once 'SQLTable.php';
include_once 'table_callrecordmaster_tbr.php';
/*
CREATE TABLE vitelitycdr
(
  calldatetime timestamp without time zone NOT NULL,
  source character varying(50) NOT NULL,
  destination character varying(50) NOT NULL,
  seconds integer NOT NULL,
  callerid character varying(100),
  disposition character varying(100),
  cost numeric(19,16),
  customerid character varying(15),
  calltype smallint,
  direction character(1)
)
*/
/*
  $row['calldatetime'],
  $row['source'],
  $row['destination'],
  $row['seconds'],
  $row['callerid'],
  $row['disposition'],
  $row['cost'],
  $row['customerid'], 
  $row['calltype'],
  $row['direction'] 
		*/
class psql_vitelitycdr extends SQLTable{
	public $table_name = 'vitelitycdr';
	public $InsertedCount = 0;
	public $DeletedCount = 0;
	public $SkippedDuplicateCount = 0;
	
	private $insertStatement;
	private $deleteStatement;
	private $checkStatement;
	private $db;
	function psql_vitelitycdr($connectString){

		$this->connectString = $connectString;
		$this->insertStatement  = <<< HEREDOC
			INSERT INTO {$this->table_name}("calldatetime","source","destination"
					,"seconds","callerid","disposition","cost","calltype","direction")
				VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9);
HEREDOC;
		
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "source" = $1
					AND "destination" = $2
					AND "calldatetime" = $3
					AND "seconds" = $4; 
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
				WHERE "source" = $1
					AND "destination" = $2
					AND "calldatetime" = $3
					AND "seconds" = $4; 
HEREDOC;
		
	}
	private $callrecordmaster_table;
	function Connect(){
		$this->callrecordmaster_table = new psql_callrecordmaster_tbr($this->connectString);
		$this->callrecordmaster_table->Connect();
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "insertvitelity", $this->insertStatement);
		pg_prepare($this->db, "deletevitelity", $this->deleteStatement);
		pg_prepare($this->db, "checkvitelity", $this->checkExistsStatement);
	}
	function Disconnect(){
		pg_close($this->db);
		$this->callrecordmaster_table->Disconnect();
	}
	function Insert($row){
		$callerid = '';
		$disposition = '';
		$cost = null;
		$customerid = '';
		$calltype = null;
		$direction = '';
		if(!isset($row['destination'])){
			throw new Exception('destination is a required field');
		}
		if(!isset($row['source'])){
			throw new Exception('source is a required field');
		}
		if(!isset($row['calldatetime'])){
			throw new Exception('calldatetime is a required field');
		}
		if(!isset($row['seconds'])){
			throw new Exception('seconds is a required field');
		}
		if(isset($row['callerid'])){
			$callerid = $row['callerid'];
		}
		if(isset($row['disposition'])){
			$disposition = $row['disposition'];
		}
		if(isset($row['cost'])){
			$cost = $row['cost'];
		}
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['calltype'])){
			$calltype = $row['calltype'];
		}
		if(isset($row['direction'])){
			$direction = $row['direction'];
		}
		
		$destinationNumber = $this->InternationalizePhoneNumber($row['destination']);
		$sourceNumber = $this->InternationalizePhoneNumber($row['source']); 
		/*"calldatetime","source","destination"
					,"seconds","callerid","disposition","cost","calltype","direction")*/
		$insertParams = array(  $row['calldatetime'],
								$sourceNumber,
								$destinationNumber,
								$row['seconds'],
								$callerid,
								$disposition,
								$cost,
								$calltype,
								$direction );
		
		$callid = <<< HEREDOC
		{$row['calldatetime']}_{$sourceNumber}_{$destinationNumber}_{$row['seconds']}
HEREDOC;
		if($this->DoesExist(array('source' => $sourceNumber,
									'destination' => $destinationNumber,
									'calldatetime' => $row['calldatetime'],
									'seconds'=>$row['seconds'])) 
				|| $this->callrecordmaster_table->DoesExist(array('callid'=>$callid))){
			$this->SkippedDuplicateCount++;
			return false;
		}
		
		$result = pg_execute($this->db, "insertvitelity", $insertParams);
		if($result){
			$this->InsertedCount++;
			return true;
		}
		return $result;
	}
	function Delete($row){
	
		$destinationNumber = $this->InternationalizePhoneNumber($row['destination']);
		$sourceNumber = $this->InternationalizePhoneNumber($row['source']); 
		$deleteParams = array($sourceNumber,$destinationNumber,
							$row['calldatetime'],$row['seconds']);
		$result = pg_execute($this->db, "deletevitelity", $deleteParams);
		if($result){
			$this->DeletedCount++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){

		$destinationNumber = $this->InternationalizePhoneNumber($row['destination']);
		$sourceNumber = $this->InternationalizePhoneNumber($row['source']); 
		$selectParams = array($sourceNumber,$destinationNumber,
							$row['calldatetime'],$row['seconds']);
		$result = pg_execute($this->db, "checkvitelity", $selectParams);
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
	
	
	function MoveToTBR(){
		
		$moveStatement = 'SELECT "fnMoveVitelityCDRToTBR"();';
		$deleteStatement = <<< HEREDOC
		DELETE FROM {$this->table_name} WHERE calldatetime || '_' || source || '_' || destination || '_' || seconds
 in (select callid from {$this->callrecordmaster_table->table_name}) 
HEREDOC;
		pg_query($this->db, $deleteStatement);
		pg_query($this->db, $moveStatement);
	}
	
	private function InternationalizePhoneNumber($phoneNumber){
		if(strlen($phoneNumber) == 10 and substr($phoneNumber,0,1) != '+'){
			$phoneNumber = '+1'.$phoneNumber;
		}
		if(strlen($phoneNumber) == 11 and substr($phoneNumber,0,1) == '1'){
			$phoneNumber = '+1'.substr($phoneNumber,1);
		}
		if(substr($phoneNumber,0,3) == '011'){
			$phoneNumber = '+'.substr($phoneNumber,3,20);
		}
		return $phoneNumber;
	}
	
	
	function GetTitles(){
		throw new Exception('This function is not implemented yet');
	}
	function GetRowView($row){
		throw new Exception('This function is not implemented yet');
	}
}

?>