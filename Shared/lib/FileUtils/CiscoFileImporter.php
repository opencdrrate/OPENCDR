<?php
include_once 'AbstractFileImporter.php';
class CiscoFileImporter extends AbstractFileImporter{
	function CiscoFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		date_default_timezone_set('America/New_York');
		list($cdrRecordType,$globalCallID_callManagerId,$globalCallID_callId,$origLegCallIdentifier,
	$dateTimeOrigination,$origNodeId,$origSpan,$origIpAddr,$callingPartyNumber,
	$callingPartyUnicodeLoginUserID,$origCause_location,$origCause_value,$origPrecedenceLevel,
	$origMediaTransportAddress_IP,$origMediaTransportAddress_Port,$origMediaCap_payloadCapability,
	$origMediaCap_maxFramesPerPacket,$origMediaCap_g723BitRate,$origVideoCap_Codec,
	$origVideoCap_Bandwidth,$origVideoCap_Resolution,$origVideoTransportAddress_IP,
	$origVideoTransportAddress_Port,$origRSVPAudioStat,$origRSVPVideoStat,$destLegIdentifier,
	$destNodeId,$destSpan,$destIpAddr,$originalCalledPartyNumber,$finalCalledPartyNumber,
	$finalCalledPartyUnicodeLoginUserID,$destCause_location,$destCause_value,$destPrecedenceLevel,
	$destMediaTransportAddress_IP,$destMediaTransportAddress_Port,$destMediaCap_payloadCapability,
	$destMediaCap_maxFramesPerPacket,$destMediaCap_g723BitRate,$destVideoCap_Codec,
	$destVideoCap_Bandwidth,$destVideoCap_Resolution,$destVideoTransportAddress_IP,
	$destVideoTransportAddress_Port,$destRSVPAudioStat,$destRSVPVideoStat,$dateTimeConnect,
	$dateTimeDisconnect,$lastRedirectDn,$pkid,$originalCalledPartyNumberPartition,
	$callingPartyNumberPartition,$finalCalledPartyNumberPartition,$lastRedirectDnPartition,
	$duration,$origDeviceName,$destDeviceName,$origCallTerminationOnBehalfOf,
	$destCallTerminationOnBehalfOf,$origCalledPartyRedirectOnBehalfOf,$lastRedirectRedirectOnBehalfOf
	,$origCalledPartyRedirectReason,$lastRedirectRedirectReason,$destConversationId,
	$globalCallId_ClusterID,$joinOnBehalfOf,$comment,$authCodeDescription,$authorizationLevel,
	$clientMatterCode,$origDTMFMethod,$destDTMFMethod,$callSecuredStatus,$origConversationId,
	$origMediaCap_Bandwidth,$destMediaCap_Bandwidth,$authorizationCodeValue,
	$outpulsedCallingPartyNumber,$outpulsedCalledPartyNumber,$origIpv4v6Addr,$destIpv4v6Addr,
	$origVideoCap_Codec_Channel2,$origVideoCap_Bandwidth_Channel2,$origVideoCap_Resolution_Channel2,
	$origVideoTransportAddress_IP_Channel2,$origVideoTransportAddress_Port_Channel2,
	$origVideoChannel_Role_Channel2,$destVideoCap_Codec_Channel2,$destVideoCap_Bandwidth_Channel2,
	$destVideoCap_Resolution_Channel2,$destVideoTransportAddress_IP_Channel2,
	$destVideoTransportAddress_Port_Channel2,$destVideoChannel_Role_Channel2) = $data;
		
		if($duration == "0" || $duration == 0){
			return false;
		}
		$assocItem = array();
		$assocItem['calldatetime'] = date('Y-m-d H:i:s', $dateTimeOrigination);
		$assocItem['duration'] = $duration;
		
		$assocItem['originatingnumber'] = str_replace('"', "", $callingPartyNumber);
		$assocItem['destinationnumber'] = str_replace('"', "", $finalCalledPartyNumber);
		
		$assocItem['carrierid'] = str_replace('"', "", $destIpv4v6Addr);
		$assocItem['sourceip'] = str_replace('"', "",$origIpv4v6Addr);
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