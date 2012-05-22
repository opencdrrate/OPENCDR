<?php
include_once 'AbstractFileImporter.php';
class TelepoFileImporter extends AbstractFileImporter{
	function TelepoFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
	/*CallID,SourceIP,OriginatingNumber,DestinationNumber,LRN,RateCenter,CallDateTime,Duration,CarrierID,CNAMDipped*/
	
		/*
		0	orgid
		1	customerid
		2	billinguserid
		3	billingphonenumber
		4	billingcostcenter
		5	billingdepartment
		6	billingrole
		7	starttime
		8	calltype
		9	callid
		10	initialcallid
		11	confirmeddurationseconds
		12	waitbeforeanswerseconds
		13	result
		14	sourceuserid
		15	sourcephonenumber
		16	sourcecostcenter
		17	sourcedepartment
		18	sourcerole
		19	sourceaptype
		20	targetuserid
		21	targetphonenumber
		22	...
		*/
		$assocItem = array();
		$assocItem['customerid'] = $data[2];
		$assocItem['calldatetime'] = $data[7];
		$assocItem['duration'] = $data[11];
		if($assocItem['duration'] == 0){
			return false;
		}
		
		if($data[8] == '1'){
			$assocItem['direction'] = 'O';
		}
		else if($data[8] == '2'){
			$assocItem['direction'] = 'I';
		}
		$assocItem['originatingnumber'] = $data[15];
		$assocItem['destinationnumber'] = $data[21];
		
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = $data[17];
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>