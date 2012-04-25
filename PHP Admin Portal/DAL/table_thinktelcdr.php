<?php
include_once 'SQLTable.php';
include_once 'table_callrecordmaster_tbr.php';
/*
CREATE TABLE thinktelcdr
(
  sourcenumber character varying(50) NOT NULL,
  destinationnumber character varying(50) NOT NULL,
  calldate timestamp without time zone NOT NULL,
  usagetype character varying(100) NOT NULL,
  rawduration numeric(9,1),
  customerid character varying(15),
  direction character(1),
  calltype smallint,
  rowid serial NOT NULL,
  CONSTRAINT thinktelcdr_pkey PRIMARY KEY (rowid )
)
*/
class psql_thinktelcdr extends SQLTable{
	public $table_name = 'thinktelcdr';
	public $InsertedCount = 0;
	public $DeletedCount = 0;
	public $SkippedDuplicateCount = 0;
	
	private $insertStatement;
	private $deleteStatement;
	private $checkStatement;
	private $db;
	function psql_thinktelcdr($connectString){
		$this->connectString = $connectString;
		$this->insertStatement  = <<< HEREDOC
			INSERT INTO {$this->table_name}("sourcenumber","destinationnumber","calldate"
					,"usagetype","rawduration","customerid","direction","calltype")
				VALUES ($1,$2,$3,$4,$5,$6,$7,$8);
HEREDOC;
		
		$this->checkExistsStatement = <<< HEREDOC
			SELECT 1 FROM {$this->table_name} 
				WHERE "sourcenumber" = $1
					AND "destinationnumber" = $2
					AND "calldate" = $3
					AND "rawduration" = $4; 
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
			DELETE FROM {$this->table_name} 
				WHERE "sourcenumber" = $1
					AND "destinationnumber" = $2
					AND "calldate" = $3
					AND "rawduration" = $4; 
HEREDOC;
		
	}
	private $callrecordmaster_table;
	function Connect(){
		$this->callrecordmaster_table = new psql_callrecordmaster_tbr($this->connectString);
		$this->callrecordmaster_table->Connect();
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		pg_prepare($this->db, "insertThinktel", $this->insertStatement);
		pg_prepare($this->db, "deleteThinktel", $this->deleteStatement);
		pg_prepare($this->db, "checkThinktel", $this->checkExistsStatement);
	}
	function Disconnect(){
		pg_close($this->db);
		$this->callrecordmaster_table->Disconnect();
	}
	function Insert($row){
		/*
			INSERT INTO {$this->table_name}("sourcenumber","destinationnumber","calldate"
					,"usagetype","rawduration","customerid","direction","calltype")
		*/
		$usageType = '';
		$customerid = '';
		$direction = '';
		$calltype = 0;
		if(!isset($row['destinationnumber'])){
			throw new Exception('destinationnumber is a required field');
		}
		if(!isset($row['sourcenumber'])){
			throw new Exception('sourcenumber is a required field');
		}
		if(!isset($row['calldate'])){
			throw new Exception('calldate is a required field');
		}
		if(!isset($row['rawduration'])){
			throw new Exception('rawduration is a required field');
		}
		if(isset($row['usagetype'])){
			$usageType = $row['usagetype'];
		}
		if(isset($row['customerid'])){
			$customerid = $row['customerid'];
		}
		if(isset($row['direction'])){
			$direction = $row['direction'];
		}
		if(isset($row['calltype'])){
			$calltype = $row['calltype'];
		}
		
		$destinationNumber = InternationalizePhoneNumber($row['destinationnumber']);
		$sourceNumber = InternationalizePhoneNumber($row['sourcenumber']); 
		$insertParams = array($sourceNumber,$destinationNumber,$row['calldate'],
						$usageType,$row['rawduration'],$customerid,
						$direction,$calltype);
		
		$callid = <<< HEREDOC
		{$row['calldate']}_{$sourceNumber}_{$destinationNumber}_{$row['rawduration']}
HEREDOC;
		if($this->DoesExist(array('sourcenumber' => $sourceNumber,
									'destinationnumber' => $destinationNumber,
									'calldate' => $row['calldate'],
									'rawduration'=>$row['rawduration'])) 
				|| $this->callrecordmaster_table->DoesExist(array('callid'=>$callid))){
			$this->SkippedDuplicateCount++;
			return false;
		}
		
		$result = pg_execute($this->db, "insertThinktel", $insertParams);
		if($result){
			$this->InsertedCount++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		$destinationNumber = InternationalizePhoneNumber($row['destinationnumber']);
		$sourceNumber = InternationalizePhoneNumber($row['sourcenumber']); 
		$deleteParams = array($sourceNumber,$destinationNumber,
							$row['calldate'],$row['rawduration']);
		$result = pg_execute($this->db, "deleteThinktel", $deleteParams);
		if($result){
			$this->DeletedCount++;
			return true;
		}
		return $result;
	}
	function DoesExist($row){
		$destinationNumber = InternationalizePhoneNumber($row['destinationnumber']);
		$sourceNumber = InternationalizePhoneNumber($row['sourcenumber']); 
		$selectParams = array($sourceNumber,$destinationNumber,
							$row['calldate'],$row['rawduration']);
		$result = pg_execute($this->db, "checkThinktel", $selectParams);
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
		$moveStatement = 'SELECT "fnMoveThinktelCDRToTBR"();';
		$deleteStatement = <<< HEREDOC
		DELETE FROM {$this->table_name} WHERE calldate || '_' || sourcenumber || '_' || destinationnumber || '_' || rawduration
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
			$phoneNumber = '+1'.$phoneNumber;
		}
		if(substr($phoneNumber,0,3) == '011'){
			$phoneNumber = '+'.substr($phoneNumber,3,20);
		}
		return $phoneNumber;
	}
}

?>