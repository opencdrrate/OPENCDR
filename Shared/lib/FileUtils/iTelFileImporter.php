<?php
include_once 'AbstractFileImporter.php';
class iTelFileImporter extends AbstractFileImporter{
	function iTelFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		list($CallDateTime, $CarrierID,$OrigNumber, $ignore,$DestNumber, $ignore,$duration) = $data;
		if($duration == 0 or $duration == '0'){
				return false;
		}
			$assocItem = array();
			$assocItem['calldatetime'] = $CallDateTime;
			$assocItem['duration'] = $duration;
			$assocItem['originatingnumber'] = $OrigNumber;
			$assocItem['destinationnumber'] = $DestNumber;
			$assocItem['carrierid'] = $CarrierID;
			
			$assocItem['cnamdipped'] = 'f';
			
			$assocItem['callid'] = $assocItem['calldatetime'] 
									. '_' . $assocItem['originatingnumber']
									. '_' . $assocItem['destinationnumber']
									. '_' . $assocItem['duration'];
									
			$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>