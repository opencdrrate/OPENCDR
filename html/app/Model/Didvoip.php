<?php
class Didvoip extends AppModel {
	var $name = 'Didvoip';
	var $useTable = false;
	var $url = 'https://www.loginto.us/VOIP/Services/APIService.asmx?WSDL';
	
	public function getDIDs($username, 
							$password, 
							$state, 
							$ratecenter = '', 
							$tier = '',
							$lata = '', 
							$npa = '',
							$nxx = '',
							$t38 = '',
							$cnam = ''
						){
		$client = new SoapClient($this->url);
		$paramsArray = array(	'login'=>$username, 
					'secret' => $password,
					'state' => $state,
					'lata' => $lata,
					'rateCenter' => $ratecenter,
					'npa' => $npa,
					'nxx' => $nxx,
					'tier' => $tier,
					't38' => $t38,
					'cnam' => $cnam);
		
		$result = $client->getDIDs(
				$paramsArray
			);
		$didResult = $result->getDIDsResult;
		if($didResult->responseMessage != 'Success'){
			$error = 'Error ' . $didResult->responseCode . ': ' . $didResult->responseMessage;
			throw new Exception($error);
			return false;
		}
		return $didResult->DIDLocators->DIDLocator;
	}
	
	public function reserveDIDs($username, $password, $tnList){
		$client = new SoapClient($this->url);
		$didList = array();
		foreach($tnList as $tn){
			$didList[] = array('epg' => 30,
								'tn' => $tn);
		}
		$params = array( 	'login'=>$username, 
							'secret' => $password,
							'didParams' => $didList);
		$result = $client->reserveDID($params);
		return $result;
	}
	
	public function assignDIDs($username, $password,$tnList, $epg){
		$client = new SoapClient($this->url);
		$didList = array();
		foreach($tnList as $tn){
			$didList[] = array('epg' => $epg,
								'tn' => $tn);
		}
		$params = array( 	'login'=>$username, 
							'secret' => $password,
							'didParams' => $didList);
		$result = $client->assignDID($params);
		$didResult = $result->assignDIDResult;
		
		if($didResult->responseMessage != 'Success'){
			throw new Exception('Error ' . $didResult->responseCode . ': ' . $didResult->responseMessage);
			return false;
		}
		$didStatuses = $didResult->DIDs->DID;
		$output = array();
		if(gettype($didStatuses) != "array"){
			$output[] = array('tn' => $didStatuses->tn, 'status' => $didStatuses->status, 'statusCode' => $didStatuses->statusCode);
		}
		else{
			foreach($didStatuses as $status){
				$output[] = array('tn' => $status->tn, 'status' => $status->status, 'statusCode' => $status->statusCode);
			}
		}
		return $output;
	}
	
	public function insert911($username, $password, $did, $address1, $address2, $city, $state, $zip, $plusFour, $callerName){
		$client = new SoapClient($this->url);
		$params = array( 	'login'=>$username, 
							'secret' => $password,
							'did'=> $did,				//Not optional
							'address1'=> $address1,		//Not optional
							'address2'=> $address2,
							'city'=> $city,				//Not optional
							'state'=> $state,			//Not optional
							'zip'=> $zip,				//Not optional
							'plusFour'=> $plusFour,
							'callerName'=> $callerName	//Not optional
						);
		$result = $client->insert911($params);
		
		return $result;
	}
}
