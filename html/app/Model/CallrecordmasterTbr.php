<?php
class CallrecordmasterTbr extends AppModel {
	var $name = 'CallrecordmasterTbr';
	var $useTable = 'callrecordmaster_tbr';
	var $primaryKey = 'rowid';
	var $displayField = 'calldatetime';
	var $actsAs = array('ImportCsv');
	var $validate = array(
		'duration' => array(
			'rule' => array('comparison', '>', 0)
		)
	);
	function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val['CallrecordmasterTbr']['calltype'])) {
				$case = $val['CallrecordmasterTbr']['calltype'];
				
				$results[$key]['CallrecordmasterTbr']['calltype'] = $this->calltype($case);
			}
		}
	
		return $results;
	}

	function loadtype($line, $type){
		//Not included here: bandwidth, vitelity, thinktel
		if($type == 'asterisk'){
			// NOTE: the fields in Master.csv can vary. This should work by default 
			//on all installations but you may have to edit the next line to match your configuration
			list($accountcode,$src, $dst, $dcontext, $clid, $channel, $dstchannel, 
			 $lastapp, $lastdata, $start, $answer, $end, $duration,
			 $billsec, $disposition, $amaflags, $uniqueID, $unknown ) = str_getcsv($line);
			
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
			
		}
		else if($type == 'itel'){
			list($CallDateTime, $CarrierID,$OrigNumber, $ignore,$DestNumber, $ignore,$duration) = str_getcsv($line);
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
			
		}
		else if($type =='aretta'){
			/*Call Date,Trunk,Call Type,Source (CallerID),Destination,Sec,Disposition,Cost*/
			list($CallDate,$Trunk,$CallType,$Source,$Destination,$Sec,$Disposition,$Cost) = str_getcsv($line);
			
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
			
		}
		else if($type =='voip'){
			list($CallType,$StartTime,$StopTime,$CallDuration,$BillDuration,$CallMinimum,
				$CallIncrement,$BasePrice,$CallPrice,
				$TransactionId,$CustomerIP,$ANI,$ANIState,$DNIS,$LRN,$DNISState,$DNISLATA,
				$DNISOCNOrig,$Tier) = str_getcsv($line, ';');
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
			
		}
		else if($type == 'cisco'){
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
		$destVideoTransportAddress_Port_Channel2,$destVideoChannel_Role_Channel2) = str_getcsv($line);
			
			$assocItem = array();
			if(!is_numeric($dateTimeOrigination)){
				return false;
			}
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
			
		}
		else if($type == 'slinger'){
			/*CallID,SourceIP,OriginatingNumber,DestinationNumber,LRN,RateCenter,CallDateTime,Duration,CarrierID,CNAMDipped*/
			list($CallID,$SourceIP,$OriginatingNumber,$DestinationNumber,$LRN,$RateCenter,
				$CallDateTime,$Duration,$CarrierID,$CNAMDipped) = str_getcsv($line);

			$assocItem['callid'] = str_replace('"', "", $CallID);
			$assocItem['calldatetime']  = str_replace('"', "", $CallDateTime);
			$assocItem['duration'] = number_format(intval(str_replace('"', "", $Duration)),0,'','');
			$assocItem['originatingnumber'] = str_replace('"', "", $OriginatingNumber);
			$assocItem['destinationnumber'] = str_replace('"', "", $DestinationNumber);
			if(str_replace('"', "", $CNAMDipped) == ""){
				$assocItem['cnamdipped'] = 'f';
			}
			else{
				$assocItem['cnamdipped'] = str_replace('"', "", $CNAMDipped);
			}
			$assocItem['carrierid'] = str_replace('"', "", $CarrierID);
			$assocItem['sourceip'] = str_replace('"', "", $SourceIP);
		}
		else if($type == 'telepo'){
			$data = str_getcsv($line);
			/*CallID,SourceIP,OriginatingNumber,DestinationNumber,LRN,RateCenter,CallDateTime,Duration,CarrierID,CNAMDipped*/
	
			/*
			0	orgid
			1	customerid
			2	billinguserid
			3	billingphonenumber
			4	billingcostcenter
			5	billingdepartment
			6	billingrole
			7	starttime
			8	calltype
			9	callid
			10	initialcallid
			11	confirmeddurationseconds
			12	waitbeforeanswerseconds
			13	result
			14	sourceuserid
			15	sourcephonenumber
			16	sourcecostcenter
			17	sourcedepartment
			18	sourcerole
			19	sourceaptype
			20	targetuserid
			21	targetphonenumber
			22	...
			*/
			$assocItem = array();
			$assocItem['customerid'] = $data[2];
			$assocItem['calldatetime'] = $data[7];
			$assocItem['duration'] = $data[11];
			if($assocItem['duration'] == 0){
				return false;
			}
			
			if($data[8] == '1'){
				$assocItem['direction'] = 'O';
			}
			else if($data[8] == '2'){
				$assocItem['direction'] = 'I';
			}
			$assocItem['originatingnumber'] = $data[15];
			$assocItem['destinationnumber'] = $data[21];
			
			$assocItem['cnamdipped'] = 'f';
			$assocItem['carrierid'] = $data[17];
			$assocItem['callid'] = $assocItem['calldatetime'] 
									. '_' . $assocItem['originatingnumber']
									. '_' . $assocItem['destinationnumber']
									. '_' . $assocItem['duration'];
			
		}
		else if($type == 'nextone'){
			$data = str_getcsv($line, ';');
			$assocItem = array();
			$assocItem['originatingnumber'] = $data[17];
			$assocItem['destinationnumber'] = $data[9];
			$assocItem['sourceip'] = $data[3];
			$assocItem['calldatetime'] = $data[0];
			$assocItem['duration'] = $data[35];
			$assocItem['cnamdipped'] = 'f';
			$assocItem['carrierid'] = $data[54];
			$assocItem['callid'] = $data[23];
			
			
			
		}
		else if($type == 'telastic'){
			/*Customer,User,Extension,Direction,CallingNumber,CalledNumber,
		StartTime,AnswerTime,EndTime,Status,TotalSeconds,Hours,Minutes,Seconds*/
			list($Customer,$User,$Extension,$Direction,$CallingNumber,$CalledNumber,
		$StartTime,$AnswerTime,$EndTime,$Status,$TotalSeconds,$Hours,$Minutes,$Seconds) = str_getcsv($line);
			
			$assocItem['callid'] = $AnswerTime
								. '_' . $CallingNumber
								. '_' . $CalledNumber
								. '_' . $TotalSeconds;
			$assocItem['calldatetime']  = $AnswerTime;
			$assocItem['duration'] = $TotalSeconds;
			if($Direction == "OUTBOUND"){
				$assocItem['direction'] = "O";
			}
			else{
				$assocItem['direction'] = "I";
			}
			$assocItem['originatingnumber'] = $CallingNumber;
			$assocItem['destinationnumber'] = $CalledNumber;
			$assocItem['cnamdipped'] = 'f';
			$assocItem['carrierid'] = "TELASTIC";
		}
		else if($type == 'netsapiens'){
			$data = str_getcsv($line);
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
			
		}
		else if($type == 'sansay'){
			$data = str_getcsv($line, ';');
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
			
		}
		if($this->find('first', array('conditions' => array('callid'=>$assocItem['callid'])))){
				return false;
		}
		return $assocItem;
	}
}
