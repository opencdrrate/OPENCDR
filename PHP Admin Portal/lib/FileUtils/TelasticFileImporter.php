<?php
include_once 'AbstractFileImporter.php';
class TelasticFileImporter extends AbstractFileImporter{
	function TelasticFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
	/*Customer,User,Extension,Direction,CallingNumber,CalledNumber,
	StartTime,AnswerTime,EndTime,Status,TotalSeconds,Hours,Minutes,Seconds*/
		list($Customer,$User,$Extension,$Direction,$CallingNumber,$CalledNumber,
	$StartTime,$AnswerTime,$EndTime,$Status,$TotalSeconds,$Hours,$Minutes,$Seconds) = $data;
			
		if($TotalSeconds == "0" || $TotalSeconds == 0){
			return false;
		}
		$item['callid'] = $AnswerTime
							. '_' . $CallingNumber
							. '_' . $CalledNumber
							. '_' . $TotalSeconds;
		$item['calldatetime']  = $AnswerTime;
		$item['duration'] = $TotalSeconds;
		if($Direction == "OUTBOUND"){
			$item['direction'] = "O";
		}
		else{
			$item['direction'] = "I";
		}
		$item['originatingnumber'] = $CallingNumber;
		$item['destinationnumber'] = $CalledNumber;
		$item['cnamdipped'] = 'f';
		$item['carrierid'] = "TELASTIC";
		
		$oldParams = array('callid' => $item['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$item);
	}
}
?>