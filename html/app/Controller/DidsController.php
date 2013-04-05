<?php
class DidsController extends AppController {
	var $name = 'Dids';
	var $uses = array('Didmaster', 'Didvoip', 'Siteconfiguration');
	
    var $helpers = array('Js' => array('Jquery'));
	var $components = array('RequestHandler');
	
	private function GetVOIPCredentials(){
		$settings = $this->Siteconfiguration->ListAll();
		return array('username' => $settings['voip_user'], 'password' => $settings['voip_pwd']);
	}
	function import(){
		if (!empty($this->data)) {
			$filename = $this->data['Document']['filename']['tmp_name'];
			$type = '';
			$messages = $this->Didmaster->import($filename, $type);
			$this->Session->setFlash(__($messages, true));
			$this->redirect(array('action' => 'index'));
		}
	}
	function countvoipdids($state = '', $ratecenter = '', $tier = '', $lata = ''){
		$voip = $this->GetVOIPCredentials();
		$username = $voip['username'];
		$password = $voip['password'];
		
		$this->layout = '';
		if($this->RequestHandler->isAjax()){
			$dids = array();
			try{
				$dids = $this->Didvoip->getDIDs($username,
														$password,
														$state,
														$ratecenter,
														$tier,
														$lata
								);
				$count = count($dids);
				$message = $count . ' records found.';
				$this->set('message', $message );
			}catch(Exception $e){
				$message = $e->getMessage();
				$this->set('message', $message );
			}
		}
	}
	
	function ratecenters($state = '', $ratecenter = '', $tier = '', $lata = ''){
		$voip = $this->GetVOIPCredentials();
		$username = $voip['username'];
		$password = $voip['password'];
		
		$this->layout = '';
		if($this->RequestHandler->isAjax()){
			try{
				$dids = $this->Didvoip->getDIDs($username,
														$password,
														$state,
														$ratecenter,
														$tier,
														$lata
								);
			}catch(Exception $e){
			}
			$ratecenters = array();
			foreach($dids as $did){
				$ratecenters[] = $did->rateCenter;
			}
		
			$ratecenters = array_unique($ratecenters);
			$this->set('ratecenters', $ratecenters);
			$this->set('selectedRatecenter', $ratecenter);
		}
	}
	
	function tiers($state = '', $ratecenter = '', $tier = '', $lata = ''){
		$voip = $this->GetVOIPCredentials();
		$username = $voip['username'];
		$password = $voip['password'];
		
		$this->layout = '';
		if($this->RequestHandler->isAjax()){
			$tiers = array();
			try{
				$dids = $this->Didvoip->getDIDs($username,
														$password,
														$state,													
														$ratecenter,
														$tier,
														$lata

								);
			}catch(Exception $e){
			}
			foreach($dids as $did){
				$tiers[] = $did->tier;
			}
		
			$tiers = array_unique($tiers);
			$this->set('tiers', $tiers);
			$this->set('selectedTier', $tier);
		}
		else{
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function latas($state = '', $ratecenter = '', $tier = '', $lata = ''){
		$voip = $this->GetVOIPCredentials();
		$username = $voip['username'];
		$password = $voip['password'];
		
		$this->layout = '';
		if($this->RequestHandler->isAjax()){
			$latas = array();
			try{
				$dids = $this->Didvoip->getDIDs($username,
														$password,
														$state,
														$ratecenter,
														$tier,
														$lata
								);
			}catch(Exception $e){
			}
			foreach($dids as $did){
				$latas[] = $did->lataId;
			}
		
			$latas = array_unique($latas);
			$this->set('latas', $latas);
			$this->set('selectedLata', $lata);
		}
		else{
			$this->redirect(array('action' => 'index'));
		}
	}
	
	function dids($state = '', $ratecenter = '', $tier = '', $lata = ''){
		$voip = $this->GetVOIPCredentials();
		$username = $voip['username'];
		$password = $voip['password'];
		
		$this->layout = '';
			try{
				$this->set('Dids', $this->Didvoip->getDIDs($username,
								$password,
								$state,
								$ratecenter,
								$tier,
								$lata));
			}
			catch(Exception $e){
				$this->set('Dids', array());
			}
	}
	
	function verifyaddvoip(){
		$voipcredentials = $this->GetVOIPCredentials();
		$username= $voipcredentials['username'];
		$password= $voipcredentials['password'];
		if (!empty($this->data)) {
			if(!$this->data['Didmaster']['customerid']){
				$this->Session->setFlash('Please choose a customer');
				$this->redirect(array('action'=>'addVoip'));
			}
			$customerid = $this->data['Didmaster']['customerid'];
			$epg = $this->data['Didmaster']['EPG'];
			$telephoneNumbers = array();
			$e911s = array();
			
			foreach($this->data['Didmaster'] as $key => $val){
				if($key != 'customerid' && $key != 'EPG'){
					if(!(strpos($key, 'E911-') === false)){
						$e911s[$val] = '1';
					}
					else if(!(strpos($key, 'did-') === false)){
						$telephoneNumbers[] = $val;
					}
				}
			}
			
			try{
				$statuses = $this->Didvoip->assignDIDs($username,
								$password,
								$telephoneNumbers,
								$epg);
				$modifiedStatuses = array();
				foreach($statuses as $status){
					if($status['statusCode'] == 100){
						$this->Didmaster->create();
						$actionMessage = '<ul>';
						if ($this->Didmaster->save(array('customerid'=>$customerid , 'did' => $status['tn']))) {
							$actionMessage .= '<li>DID saved to ' . $customerid . '</li>';
						}
						else{
							$actionMessage .= '<li>DID not saved</li>';
						}
						
						if(isset($e911s[$status['tn']])){
							$address1 = '260'; 
							$address2 = ''; 
							$city = 'New York City';
							$state = 'New York'; 
							$zip = '10007'; 
							$plusFour = '';
							$callerName = 'Hoobastank';
							
							$e911result = $this->Didvoip->insert911($username,$password,$status['tn'],
								$address1, $address2, $city, 
								$state, $zip, $plusFour, $callerName);
							if($e911result->insert911Result->responseCode == 100){
								$actionMessage .= '<li>E911 successfully assigned</li>';
							}
							else{
								$actionMessage .= '<li>Error assigning E911('.$e911result->insert911Result->responseCode.')'.$e911result->insert911Result->responseMessage.'</li>';
							}
						}
						$actionMessage .= '</ul>';
						$status['actionMessage'] = $actionMessage;
					}
					else{
						$status['actionMessage'] = 'DID not saved';
					}
					$modifiedStatuses[] = $status;
				}
				$this->set('statuses', $modifiedStatuses);
			}
			catch(Exception $e){
				$this->Session->setFlash($e->getMessage());
				$this->set('statuses', array());
			}
		}
	}
	
	function index() {
		$this->Didmaster->recursive = 0;
		$this->set('didmasters', $this->paginate());
	}

	function add() {
		$this->set('customers', $this->Didmaster->Customer->find('list'));
		if (!empty($this->data)) {
			$this->Didmaster->create();
			if ($this->Didmaster->save($this->data)) {
				$this->Session->setFlash(__('The didmaster has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The didmaster could not be saved. Please, try again.', true));
			}
		}
	}
	
	function addVoip(){
	}
	
	function edit($id = null) {
		$this->set('customers', $this->Didmaster->Customer->find('list'));
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid didmaster', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Didmaster->save($this->data)) {
				$this->Session->setFlash(__('The didmaster has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The didmaster could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Didmaster->read(null, $id);
			$this->set('did', $this->data);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for didmaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Didmaster->delete($id)) {
			$this->Session->setFlash(__('Didmaster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Didmaster was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function tocsv(){
		$this->set('data', $this->Didmaster->find('all'));
		$this->layout = '';
	}
	
	function choosecustomer(){
			$this->set('customers', $this->Didmaster->Customer->find('list'));
	}
}
