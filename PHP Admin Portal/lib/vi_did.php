<?php
include_once 'debug.php';

/*
Ok, the Login/Secret that you can use is DTHTest/w$dlt3st

The CustomerID is 211 for now, but this can be changed whenever. It may not even be relevant.
*/

$url = 'https://www.loginto.us/VOIP/Services/APIService.asmx?WSDL';

#$didResponse = $client->auditDIDs(array('login'=>'DTHTest', 'secret' => 'w$dlt3st'));
#print_r($didResponse);
#$auditResponse = $didResponse->auditDIDsResult;
#print_r($auditResponse->DIDs);
#$didList = $didResponse['DIDs'];
class VI_Client{
	var $client;
	var $login;
	var $secret;
	
	function VI_Client($user, $pwd){
		global $url;
		$this->client = new SoapClient($url);
		$this->login = $user;
		$this->secret = $pwd;
	}
	/*
	public function GetDidCount(){
		print_debug('Getting a count of DIDs : ' . $this->login);
		$result = $this->client->getDIDCount(
			array(	'login'=>$this->login, 
					'secret' => $this->secret));
		$DIDCountResult = $result->getDIDCountResult;
		$DIDCountsObj = $DIDCountResult->DIDCounts;
		return $DIDCountResult;
	}*/
	
	public function getDIDs($state, $lata, $ratecenter, $npa, $nxx, $tier, $t38, $cnam){
		print_debug('Getting a DID list');
		$result = $this->client->getDIDs(
			array(	'login'=>$this->login, 
					'secret' => $this->secret,
					'state' => $state,
					'lata' => $lata,
					'rateCenter' => $ratecenter,
					'npa' => $npa,
					'nxx' => $nxx,
					'tier' => $tier,
					't38' => $t38,
					'cnam' => $cnam)
			);
		$didResult = $result->getDIDsResult;
		if($didResult->responseMessage != 'Success'){
			return false;
		}
		return $didResult->DIDLocators->DIDLocator;
	}
}
?>