<?php
include_once 'AbstractFileImporter.php';
class SlingerFileImporter extends AbstractFileImporter{
	function SlingerFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		/*CallID,SourceIP,OriginatingNumber,DestinationNumber,LRN,RateCenter,CallDateTime,Duration,CarrierID,CNAMDipped*/
		list($CallID,$SourceIP,$OriginatingNumber,$DestinationNumber,$LRN,$RateCenter,
			$CallDateTime,$Duration,$CarrierID,$CNAMDipped) = $data;
		if($Duration == "\"0\"" || $Duration == 0){
			return false;
		}
		$item['callid'] = str_replace('"', "", $CallID);
		$item['calldatetime']  = str_replace('"', "", $CallDateTime);
		$item['duration'] = number_format(str_replace('"', "", $Duration),0,'','');
		$item['originatingnumber'] = str_replace('"', "", $OriginatingNumber);
		$item['destinationnumber'] = str_replace('"', "", $DestinationNumber);
		if(str_replace('"', "", $CNAMDipped) == ""){
			$item['cnamdipped'] = 'f';
		}
		else{
			$item['cnamdipped'] = str_replace('"', "", $CNAMDipped);
		}
		$item['carrierid'] = str_replace('"', "", $CarrierID);
		$item['sourceip'] = str_replace('"', "", $SourceIP);
		
		$oldParams = array('callid' => $item['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$item);
	}
}
?>