<?php
include_once 'AbstractFileImporter.php';
class ArettaFileImporter extends AbstractFileImporter{
	function ArettaFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		/*Call Date,Trunk,Call Type,Source (CallerID),Destination,Sec,Disposition,Cost*/
		list($CallDate,$Trunk,$CallType,$Source,$Destination,$Sec,$Disposition,$Cost) = $data;
		
		$assocItem = array();
		$assocItem['originatingnumber'] = $Source;
		$assocItem['destinationnumber'] = $Destination;
		$assocItem['sourceip'] = '';
		$assocItem['calldatetime'] = $CallDate;
		$assocItem['duration'] = $Sec;
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = 'ARETTA';
		$assocItem['wholesaleprice'] = $Cost;
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		
		if($CallType == "Outbound"){
			$assocItem['direction'] = "O";
			$assocItem['calltype'] = '35';
		}
		else{
			$assocItem['direction'] = "I";
			$assocItem['calltype'] = '15';
		}
		if($assocItem['duration'] == 0){
			return false;
		}
			$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>