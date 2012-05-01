<?php
include_once 'SQLTable.php';
/*callrecordmaster_tbr*/
/*
CREATE TABLE callrecordmaster_tbr
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
  rowid serial NOT NULL,
  CONSTRAINT callrecordmaster_tbr_pkey PRIMARY KEY (callid ),
  CONSTRAINT callrecordmaster_tbr_rowid_key UNIQUE (rowid )
)
*/
class psql_callrecordmaster_tbr extends SQLTable{
	public $table_name = 'callrecordmaster_tbr';
	public $IsConnected = false;
	private $connectString;
	private $db;
	private $insertStatement;
	private $checkStatement;
	private $deleteStatement;
	
	public $InsertedCount = 0;
	public $DeletedCount = 0;
	public $SkippedDurationCount = 0;
	public $SkippedDuplicateCount = 0;
	function psql_callrecordmaster_tbr($connectString){
		$this->connectString = $connectString;
		$this->insertStatement = <<< HEREDOC
		INSERT INTO {$this->table_name}(callid, customerid, calltype, calldatetime, duration, direction, 
            sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
            ratecenter, carrierid, wholesalerate, wholesaleprice)
			VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15)
HEREDOC;
		$this->checkStatement = <<< HEREDOC
		SELECT 1 FROM {$this->table_name} 
				WHERE callid = $1;
HEREDOC;
		$this->deleteStatement = <<< HEREDOC
		DELETE FROM {$this->table_name} WHERE callid = $1
HEREDOC;
	}
	
	function Connect(){
		$this->db = pg_connect($this->connectString);
		set_time_limit(0);
		if(!$this->db){
			throw new Exception("Error in connection: " . pg_last_error());
		}
		set_time_limit(0);
		pg_prepare($this->db, "insert", $this->insertStatement);
		pg_prepare($this->db, "check", $this->checkStatement);
		pg_prepare($this->db, "delete", $this->deleteStatement);
	}
	function Disconnect(){
		pg_close($this->db);
	}
	
	function Insert($row){
		$callid = '';
		$customerid = '';
		$calltype = null;	
		$calldatetime = '';
		$duration = '';
		$direction = '';
		$sourceip = '';
		$originatingnumber = '';
		$destinationnumber = '';
		$lrn = '';
		$cnamdipped = '';
		$ratecenter = '';
		$carrierid = '';
		$wholesalerate = null;
		$wholesaleprice = null;
		
		if(!isset($row['duration'])){
			throw new Exception('\'duration\' field required for insert.');
		}else{
			$duration = $row['duration'];
		}
		if(!isset($row['calldatetime'])){
			throw new Exception('\'calldatetime\' field required for insert.');
		}else{
			$calldatetime = $row['calldatetime'];
		}
		if(!isset($row['originatingnumber'])){
			throw new Exception('\'originatingnumber\' field required for insert.');
		}else{
			$originatingnumber = $row['originatingnumber'];
		}
		if(!isset($row['destinationnumber'])){
			throw new Exception('\'destinationnumber\' field required for insert.');
		}else{
			$destinationnumber = $row['destinationnumber'];
		}
		if(!isset($row['callid']) || empty($row['callid']) ){
			$callid = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		}else{
			$callid = $row['callid'];
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
		if(isset($row['sourceip'])){
			$sourceip = $row['sourceip'];
		}
		if(isset($row['lrn'])){
			$lrn = $row['lrn'];
		}
		if(isset($row['cnamdipped'])){
			$cnamdipped = $row['cnamdipped'];
		}else{
			$cnamdipped = 'f';
		}
		if(isset($row['ratecenter'])){
			$ratecenter = $row['ratecenter'];
		}
		if(isset($row['carrierid'])){
			$carrierid = $row['carrierid'];
		}
		if(isset($row['wholesalerate'])){
			$wholesalerate = $row['wholesalerate'];
		}
		if(isset($row['wholesaleprice'])){
			$wholesaleprice = $row['wholesaleprice'];
		}
		
		if($row['duration'] == 0 || $row['duration'] == '0' ){
			$this->SkippedDurationCount += 1;
			return false;
		}
		if($this->DoesExist(array('callid'=>$callid))){
			$this->SkippedDuplicateCount += 1;
			return false;
		}

		$insertParams = array($callid,$customerid,$calltype,$calldatetime,
								$duration,$direction,$sourceip,$originatingnumber,
								$destinationnumber,$lrn,$cnamdipped,$ratecenter,
								$carrierid,$wholesalerate,$wholesaleprice);
		
		$result = pg_execute($this->db, "insert", $insertParams);
		if($result){
			$this->InsertedCount++;
			return true;
		}
		return $result;
	}
	function Delete($row){
		$callid = $row['callid'];
		$deleteParams = array($callid);
		$result = pg_execute($this->db, "delete", $deleteParams);
		if($result){
			$this->DeletedCount++;
			return true;
		}
		return false;
	}
	function DoesExist($row){
		$result = pg_execute($this->db, "check", array($row['callid']));
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
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
	
	function SelectUncat($offset = 0, $limit = 0){
		$viewQuery = 'SELECT callid, customerid, calldatetime, duration, direction, 
		   sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
		   ratecenter, carrierid
		   FROM callrecordmaster_tbr
		   where calltype is null';
		  
		$limitedQuery = $viewQuery;
		if($limit > 0){
			$limitedQuery .= " LIMIT "
				. $limit
				. " OFFSET "
				. $offset	
				. ";";
		}
		$queryResults = pg_query($this->db, $limitedQuery);
		$allArrayResults = array();
		
		while($row = pg_fetch_assoc($queryResults)){
			$allArrayResults[] = $row;
		}
		return $allArrayResults;
		
	}
	
	function SelectRatingQueue($offset = 0, $limit = 0){
		
		$query = <<< HEREDOC
		SELECT callid, customerid, calltype, calldatetime, duration, direction, 
		   sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
		   ratecenter, carrierid
			FROM {$this->table_name} WHERE calltype is not NULL
HEREDOC;
		$limitedQuery = $query;
		if($limit > 0){
			$limitedQuery .= " LIMIT "
				. $limit
				. " OFFSET "
				. $offset	
				. ";";
		}
		$queryResults = pg_query($this->db, $limitedQuery);
		$allArrayResults = array();
		
		while($row = pg_fetch_assoc($queryResults)){
			$callType = $row['calltype'];

			if($callType == '5'){
				$row['calltype'] = 'Intrastate';
			}
			else if($callType == '10'){
				$row['calltype'] = 'Interstate';
			}
			else if($callType == '15'){
				$row['calltype'] = 'Tiered Origination';
			}
			else if($callType == '20'){
				$row['calltype'] = 'Termination of Indeterminate Jurisdiction';
			}
			else if($callType == '25'){
				$row['calltype'] = 'International';
			}
			else if($callType == '30'){
				$row['calltype'] = 'Toll-free Origination';
			}
			else if($callType == '35'){
				$row['calltype'] = 'Simple Termination';
			}
			else{
				$row['calltype'] = 'Unknown';
			}
			$allArrayResults[] = $row;
		}
		return $allArrayResults;
	}
	
	function CountRatingQueue(){
		$queryNumberofRows = 'SELECT count(*) FROM callrecordmaster_tbr WHERE calltype is not NULL;';
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		$numberOfRows = $numberOfRowsArray[0];
		return $numberOfRows;
	}
	
	function CountUncat(){
		$queryNumberofRows = 'SELECT count(*) FROM callrecordmaster_tbr WHERE calltype is NULL;';
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		$numberOfRows = $numberOfRowsArray[0];
		return $numberOfRows;
	}
}
?>