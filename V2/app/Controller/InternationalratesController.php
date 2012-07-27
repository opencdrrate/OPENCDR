<?php
class InternationalratesController extends AppController {

	var $name = 'Internationalrates';
	var $uses = array('Internationalratemaster');
	
	function index($customerid = null) {
		$this->Internationalratemaster->recursive = 0;
		if(!empty($customerid)){
			$this->paginate=array('conditions' => array('customerid' => $customerid));
			$this->set('customerid', $customerid);
		}
		else{
			$this->Session->setFlash(__('Invalid Customer ID', true));
			$this->redirect('/');
		}
		$this->set('internationalratemasters', $this->paginate());
	}

	function delete($id = null, $customerid = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/');
		}
		if (!$customerid) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/');
		}
		if ($this->Internationalratemaster->delete($id)) {
			$this->Session->setFlash(__('Internationalratemaster deleted', true));
			$this->redirect(array('action'=>'index', $customerid));
		}
		$this->Session->setFlash(__('Internationalratemaster was not deleted', true));
		$this->redirect(array('action' => 'index', $customerid));
		
	}
	function tocsv($customerid = null){
		if(!empty($customerid)){
				$this->set('data', $this->Internationalratemaster->find('all', 
					array('conditions' => 
						array('customerid' => $customerid)
					)
				)
			);
		}
		else{
			$this->set('data', $this->Internationalratemaster->find('all'));
		}
		$this->layout = '';
	}
	
	
	function import($customerid = null){
		$this->set('customerid', $customerid);

		if (!empty($this->data)) {
			$customerid = $this->data['Internationalratemaster']['customerid'];
			$error = $this->data['Document']['filename']['error'];
			if($error == 1){
				$this->Session->setFlash(__('Max file upload size exceeded.', true));
			}
			else{
				$filename = $this->data['Document']['filename']['tmp_name'];
				$messages = $this->Internationalratemaster->import($filename, $customerid);
				$this->Session->setFlash(__($messages, true));
				$this->redirect(array('action' => 'index', $customerid));
			}
		}
		if(!$customerid){
			$this->Session->setFlash(__('Invalid Page', true));
			$this->redirect('/');
		}
	}
}
