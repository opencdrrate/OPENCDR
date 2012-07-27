<?php

class VitelitycdrController extends AppController{
	var $name = 'Vitelitycdr';
	var $uses = array('VitelityAPI', 'SiteConfiguration','CallrecordmasterTbr');
	
	function index(){
		$siteconfiguration = $this->SiteConfiguration->ListAll();
		$user = $siteconfiguration['vitelity_username'];
		$password = $siteconfiguration['vitelity_password'];
		$cdrs = $this->VitelityAPI->FetchCDR($user,$password);
		
		$updateFailed = 0;
		$recordAdded = 0;
		$zeroDuration = 0;
		$totalRecords = count($cdrs);
		
		foreach($cdrs as $cdr){
			if($cdr['CallrecordmasterTbr']['duration'] == 0){
				$zeroDuration++;
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
				echo "Update failed ". $updateFailed . " CDR<br>";
				echo "Skipped due to Zero duration : " . $zeroDuration . "<br>";
				echo "Successfully Processed ". $recordAdded . " CDR<br>";
				echo '<br>';
		$this->layout = '';
	}
}

?>