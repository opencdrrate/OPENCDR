<?php
include_once 'AbstractFileImporter.php';
class ThinktelFileImporter extends AbstractFileImporter{
	function ThinktelFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		/*Billing Number,Source Number,Destination Number,Call Date,Rounded Call ` (Seconds),Usage Type,
Billed Amount (Dollars),Source Location,Destination Location,Rate (Dollars Per Minute),Label,
Raw Duration*/
		list($BillingNumber, $SourceNumber,$DestinationNumber,$CallDate,$Duration, 
				$Type,$Amount,$SrcLocation,$DestLocation,$Rate, $Label, $RawDuration) = $data;
			if($Duration == 0 or $Duration == '0'){
				return false;
			}
			$assocItem = array();
			$assocItem['calldate'] = $CallDate;
			$assocItem['rawduration'] = $Duration;
			$assocItem['sourcenumber'] = $SourceNumber;
			$assocItem['destinationnumber'] = $DestinationNumber;
			
			$assocItem['callid'] = $assocItem['calldate'] 
									. '_' . $assocItem['sourcenumber']
									. '_' . $assocItem['destinationnumber']
									. '_' . $assocItem['rawduration'];

		$oldParams = array('calldate' => $assocItem['calldate'],
							'sourcenumber' => $assocItem['sourcenumber'],
							'destinationnumber' => $assocItem['destinationnumber'],
							'rawduration' => $assocItem['rawduration']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>