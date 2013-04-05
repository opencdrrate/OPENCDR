<?php

class AsteriskServersController extends AppController{
	var $name = 'AsteriskServers';
	var $uses = array('AsteriskServer','CallrecordmasterTbr');
	function index(){
		$this->set('asteriskservers', $this->paginate());
	}
	
    public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('massimport','index'); // Letting users register themselves
	}
	
	function add(){
		if (!empty($this->data)) {
			$this->AsteriskServer->create();
			if ($this->AsteriskServer->save($this->data)) {
				$this->Session->setFlash(__('The server has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The server could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid page', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->AsteriskServer->save($this->data)) {
				$this->Session->setFlash(__('The server has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The server could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->AsteriskServer->read(null, $id);
		}
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for server', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->AsteriskServer->delete($id, true)) {
			$this->Session->setFlash(__('Server deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Server was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function massimport(){
		$this->layout = '';
		$asteriskservers = $this->AsteriskServer->find('all');
		
		$failedRows = array();
		
		foreach($asteriskservers as $server){
			$servername = $server['AsteriskServer']['servername'];
			$host = $server['AsteriskServer']['serveripordns'];
			$user = $server['AsteriskServer']['mysqllogin'];
			$password = $server['AsteriskServer']['mysqlpassword'];
			$database = $server['AsteriskServer']['cdrdatabase'];
			$port = $server['AsteriskServer']['mysqlport'];
			$table = $server['AsteriskServer']['cdrtable'];
			$active = $server['AsteriskServer']['active'];
			if($active == 0){
				echo 'Skipping inactive server :'. $servername . '<br>';
				continue;
			}
			#connect to server
				$connection = new mysqli($host,$user,$password,$database, $port);
				if ($connection->connect_errno) {
					$msg = "Failed to connect to ".$servername.": (" . $connection->connect_errno . ") " . $connection->connect_error . '<br>';
					echo $msg . '<br>';
					continue;
				}
				else{
					echo 'Connected to : ' . $connection->host_info . '<br>';
				}
			
			#fetch top 1000 rows
				$sqlQuery = "SELECT * from ".$table." where amaflags <> 100 and uniqueid > 0 order by calldate LIMIT 1000";
				$res = $connection->query($sqlQuery);
				$cdrs = array();
				
				if (!$res) {
					echo "Could not successfully run query (".$sqlQuery .") from DB: " . mysql_error() . '<br>';
					continue;
				}
				else{
					echo 'Fetching CDR from : ' . $servername . '<br>';
				}
				while($row = $res->fetch_assoc()){
					$cdrs[] = $row;
				}
				$zeroDurationCalls = 0;
				$insertedCount = 0;
				$updateFailed = 0;
				
				foreach($cdrs as $cdr){	
					$callid = $cdr['uniqueid'];
					$customerid = $cdr['accountcode'];
					$calldatetime = $cdr['calldate'];
					$duration = $cdr['billsec'];
					$originatingnumber = $cdr['src'];
					$destinationnumber = $cdr['dst'];
					$carrierid = $cdr['dstchannel'];
					
					#update amaflags
						$newAmaflag = 100;
						$oldCallid = $callid;
						$updateString = <<< HEREDOC
						UPDATE {$table} SET amaflags={$newAmaflag}
							WHERE uniqueid='{$oldCallid}';
HEREDOC;
						$res = $connection->query($updateString);
						
						$verifyString = <<< HEREDOC
						SELECT * from {$table}
						WHERE uniqueid = '{$oldCallid}' and amaflags = {$newAmaflag};
HEREDOC;
						$verifyResult = $connection->query($verifyString);
						if ($verifyResult->num_rows > 0){
							#update successsful
						}
						else{
							#update failed
							$failedRows[] = $cdr;
							$updateFailed++;
							continue;
						}
					
					$randomdigits = $this->RandomID();
					$newcallid = $callid . '_' . $randomdigits;
					$tbrRow = array('callid' => $newcallid,'customerid' => $customerid,
									'calldatetime' => $calldatetime,'duration' => $duration,
									'originatingnumber' => $originatingnumber,'destinationnumber' => $destinationnumber,
									'carrierid' => $carrierid);
					if($duration == 0){
						$zeroDurationCalls++;
						continue;
					}
					$data = array();
					$data['CallrecordmasterTbr'] = $tbrRow;
					
					#insert into callrecordmaster tbr
					$this->CallrecordmasterTbr->Create();
					if ($this->CallrecordmasterTbr->save($data)) {
						$insertedCount++;
					}
					else{
						$updateFailed++;
					}
				}
				echo 'Total CDR Fetched: ' . count($cdrs) . '<br>';
				echo "Update failed ". $updateFailed . " CDR<br>";
				echo "Skipped due to Zero duration : " . $zeroDurationCalls . "<br>";
				echo "Successfully Processed ". $insertedCount . " CDR<br>";
				echo '<br>';
		}
	}
	private function RandomID(){
		$number = '';
		for($i = 0; $i < 10; $i++){
			$number .= rand(0,9);
		}
		return $number;
	}
	function resetamaflags(){
		$this->layout = '';
		$asteriskservers = $this->AsteriskServer->find('all');
		
		foreach($asteriskservers as $server){
			$servername = $server['AsteriskServer']['servername'];
			$host = $server['AsteriskServer']['serveripordns'];
			$user = $server['AsteriskServer']['mysqllogin'];
			$password = $server['AsteriskServer']['mysqlpassword'];
			$database = $server['AsteriskServer']['cdrdatabase'];
			$port = $server['AsteriskServer']['mysqlport'];
			$table = $server['AsteriskServer']['cdrtable'];
			$active = $server['AsteriskServer']['active'];
			if($active == 0){
				echo 'Skipping inactive server :'. $servername . '<br>';
				continue;
			}
			#connect to server
				$connection = new mysqli($host,$user,$password,$database, $port);
				
			$sqlStatement = <<< HEREDOC
				UPDATE {$table} SET amaflags = 0 WHERE amaflags = 100
HEREDOC;
			$res = $connection->query($sqlStatement);
			if (!$res){
					echo "Could not succesfully run query " . $sqlStatement . " : " . mysql_error() . '<br>';
			}
			else{
				echo $servername.' AMAFlags reset!<br>';
			}
		}
	}
}
?>