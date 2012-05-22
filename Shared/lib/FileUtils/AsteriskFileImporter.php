<?php
include_once 'AbstractFileImporter.php';
class AsteriskFileImporter extends AbstractFileImporter{
	function AsteriskFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		// NOTE: the fields in Master.csv can vary. This should work by default 
	//on all installations but you may have to edit the next line to match your configuration
    list($accountcode,$src, $dst, $dcontext, $clid, $channel, $dstchannel, $lastapp, $lastdata, $start, $answer, $end, $duration,
     $billsec, $disposition, $amaflags, $uniqueID, $unknown ) = $data;
		if($billsec == 0 or $billsec == '0'){
			return false;
		}
		$assocItem = array();
		$assocItem['customerid'] = $accountcode;
		$assocItem['calldatetime'] = $start;
		$assocItem['duration'] = $billsec;
		$assocItem['originatingnumber'] = $src;
		$assocItem['destinationnumber'] = $dst;
		$assocItem['carrierid'] = $dstchannel;
		
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