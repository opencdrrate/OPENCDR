<?php

class VitelitycdrController extends AppController{
	var $name = 'Vitelitycdr';
	var $uses = array('VitelityAPI', 'Siteconfiguration','CallrecordmasterTbr');
	
    public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index'); // Letting users register themselves
	}
	
	function index(){
		$siteconfiguration = $this->Siteconfiguration->ListAll();
		$user = $siteconfiguration['vitelity_username'];
		$password = $siteconfiguration['vitelity_password'];
		$cdrs = $this->VitelityAPI->FetchCDR($user,$password);
		
		$updateFailed = 0;
		$recordAdded = 0;
		$zeroDuration = 0;
		$duplicateCount = 0;
		$totalRecords = count($cdrs);
		
		foreach($cdrs as $cdr){
			if($cdr['CallrecordmasterTbr']['duration'] == 0){
				$zeroDuration++;
				continue;
			}
			if(!preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$cdr['CallrecordmasterTbr']['calldatetime']) ){
				$updateFailed++;
				continue;
			}
			$cdr['CallrecordmasterTbr']['sourceip'] = substr($cdr['CallrecordmasterTbr']['sourceip'], 0, 15);
			
			$duplicate = $this->CallrecordmasterTbr->find('first', array('conditions' => array('callid' => $cdr['CallrecordmasterTbr']['callid'])));
			if(!empty($duplicate)){
				$duplicateCount++;
				continue;
			}
			$this->CallrecordmasterTbr->Create();
			if($this->CallrecordmasterTbr->save($cdr)){
				$recordAdded++;
			}
			else{
				$updateFailed++;
				continue;
			}
		}
				echo 'Total CDR Fetched: ' . $totalRecords . '<br>';
				echo 'Invalid format: ' . $updateFailed . '<br>';
				echo "Duplicate records found ". $duplicateCount . " CDR<br>";
				echo "Skipped due to Zero duration : " . $zeroDuration . "<br>";
				echo "Successfully Processed ". $recordAdded . " CDR<br>";
				echo '<br>';
		$this->layout = '';
	}
}

?>