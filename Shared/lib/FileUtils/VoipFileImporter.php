<?php
include_once 'AbstractFileImporter.php';
class VoipFileImporter extends AbstractFileImporter{
	function VoipFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		/*CallType,StartTime,StopTime,CallDuration,BillDuration,CallMinimum,CallIncrement,BasePrice,CallPrice,
	TransactionId,CustomerIP,ANI,ANIState,DNIS,LRN,DNISState,DNISLATA,DNISOCNOrig,Tier*/
		list($CallType,$StartTime,$StopTime,$CallDuration,$BillDuration,$CallMinimum,$CallIncrement,$BasePrice,$CallPrice,
	$TransactionId,$CustomerIP,$ANI,$ANIState,$DNIS,$LRN,$DNISState,$DNISLATA,$DNISOCNOrig,$Tier) = $data;
		$assocItem = array();
		switch ($CallType){
			case 'TERM_EXT_US_INTER':
				$assocItem['calltype'] = 25;
				break;
			case '800OrigC':
				$assocItem['calltype'] = 30;
				break;
			case '800OrigE':
				$assocItem['calltype'] = 30;
				break;
			case 'Orig-Tiered':
				$assocItem['calltype'] = 15;
				break;
			case 'TERM_INTERSTATE':
				$assocItem['calltype'] = 10;
				break;
			case 'TERM_INTRASTATE':
				$assocItem['calltype'] = 5;
				break;
		}
		$assocItem['calldatetime'] = $StartTime;
		$assocItem['duration'] = intval($CallDuration);
		
		$assocItem['originatingnumber'] = $ANI;
		$assocItem['destinationnumber'] = $DNIS;
		
		$assocItem['lrn'] = $LRN;
		$assocItem['ratecenter'] = $Tier;
		$assocItem['carrierid'] = 'VI';
		$assocItem['sourceip'] = $CustomerIP;
		$assocItem['cnamdipped'] = 'f';
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		if($assocItem['duration'] == '0'){
			return false;
		}
			$oldParams = array('callid' => $assocItem['callid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$assocItem);
	}
}
?>