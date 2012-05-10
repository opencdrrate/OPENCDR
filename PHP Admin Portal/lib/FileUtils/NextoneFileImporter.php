<?php
include_once 'AbstractFileImporter.php';
class NextoneFileImporter extends AbstractFileImporter{
	function NextoneFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
	$assocItem = array();
		$assocItem['originatingnumber'] = $data[17];
		$assocItem['destinationnumber'] = $data[9];
		$assocItem['sourceip'] = $data[3];
		$assocItem['calldatetime'] = $data[0];
		$assocItem['duration'] = $data[35];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = $data[6];
		$assocItem['callid'] = $data[23];
		if($assocItem['duration'] == 0){
			return false;
		}
		
		$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>