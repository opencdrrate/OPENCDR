<?php
include_once 'AbstractFileImporter.php';
class SansayFileImporter extends AbstractFileImporter{
	function SansayFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		if(count($data) < 55){
			return false;
		}
		$assocItem = array();
		$assocItem['originatingnumber'] = $data[15];
		$assocItem['destinationnumber'] = $data[17];
		$assocItem['calldatetime'] = $data[6];
		$assocItem['duration'] = $data[54];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = "";
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];

		if($assocItem['duration'] == '0' || $assocItem['duration'] == 0){
			return false;
		}
		
		$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>