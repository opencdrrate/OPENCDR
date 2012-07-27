<?php
class Thinktelcdr extends AppModel{
	var $name = 'Thinktelcdr';
	var $primaryKey = 'rowid';
	var $useTable = 'thinktelcdr';
	var $actsAs = array('ImportCsv');
	
	function loadtype($line,$type){
	/*Billing Number,Source Number,Destination Number,Call Date,Rounded Call ` (Seconds),Usage Type,
Billed Amount (Dollars),Source Location,Destination Location,Rate (Dollars Per Minute),Label,
Raw Duration*/
		list($BillingNumber, $SourceNumber,$DestinationNumber,$CallDate,$Duration, 
				$Type,$Amount,$SrcLocation,$DestLocation,$Rate, $Label, $RawDuration) = str_getcsv($line,',');
			if(!is_numeric($RawDuration)){
				return false;
			}
			if($RawDuration == 0 or $RawDuration == '0'){
				return false;
			}
			$assocItem = array();
			$assocItem['calldate'] = $CallDate;
			$assocItem['rawduration'] = $RawDuration;
			$assocItem['sourcenumber'] = $SourceNumber;
			$assocItem['destinationnumber'] = $DestinationNumber;
			$assocItem['usagetype'] = $Type;
			$assocItem['callid'] = $assocItem['calldate'] 
									. '_' . $assocItem['sourcenumber']
									. '_' . $assocItem['destinationnumber']
									. '_' . $assocItem['rawduration'];
			return $assocItem;
	}
	
	function MoveToCdr(){
		$numberofdetails = $this->find('count');
		$moveString = 'SELECT "fnMoveThinktelCDRToTBR"();';
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->rawQuery($moveString);
		return $numberofdetails . ' items inserted.';
	}
}
?>