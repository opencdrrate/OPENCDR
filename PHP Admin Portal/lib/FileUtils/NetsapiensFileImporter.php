<?php
include_once 'AbstractFileImporter.php';
class NetsapiensFileImporter extends AbstractFileImporter{
	function NetsapiensFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		if(count($data) == 0 || !isset($data[1])){
			return false;
		}
		$assocItem = array();

		if(empty($data[2])){
			if(empty($data[5])){
				$assocItem['customerid'] = $data[4];
			}
			else{
				$assocItem['customerid'] = $data[5];
			}
		}
		else{
			if(empty($data[3])){
				$assocItem['customerid'] = $data[4];
			}
			else{
				$assocItem['customerid'] = $data[3];
			}
		}
		$assocItem['calldatetime'] = $data[0];
		$assocItem['duration'] = $data[1];
		
		$assocItem['originatingnumber'] = $data[6];
		$assocItem['destinationnumber'] = $data[7];
		
		$assocItem['cnamdipped'] = 'f';
		
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
								
		if($assocItem['duration'] == 0){
			return false;
		}
		$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>