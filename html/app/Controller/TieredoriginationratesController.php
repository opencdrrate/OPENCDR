<?php
class TieredoriginationratesController extends AppController {

	var $name = 'Tieredoriginationrates';
	var $uses = array('Tieredoriginationratemaster');
	function index($customerid = null) {
		$this->Tieredoriginationratemaster->recursive = 0;
		if(!empty($customerid)){
			$this->paginate=array('conditions' => array('customerid' => $customerid));
			$this->set('customerid', $customerid);
		}
		else{
			$this->Session->setFlash(__('Invalid page', true));
			$this->redirect('/');
		}
		$this->set('tieredoriginationratemasters', $this->paginate());
	}

	function delete($id = null, $customerid = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/Wholesalerates');
		}
		if (!$customerid) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/Wholesalerates');
		}
		if ($this->Tieredoriginationratemaster->delete($id)) {
			$this->Session->setFlash(__('Item deleted', true));
			$this->redirect(array('action' => 'index', $customerid));
		}
		$this->Session->setFlash(__('Item not deleted', true));
		$this->redirect(array('action' => 'index', $customerid));
	}
	function tocsv($customerid = null){
		if(!empty($customerid)){
			$this->set('data', $this->Tieredoriginationratemaster->find('all', 
				array('conditions' => 
					array('customerid' => $customerid))));
		}
		else{
			$this->set('data', $this->Tieredoriginationratemaster->find('all'));
		}
		$this->layout = '';
	}
	
	
	function import($customerid = null){
		$this->set('customerid', $customerid);	
		if (!empty($this->data)) {
			$customerid = $this->data['Tieredoriginationrates']['customerid'];
			$error = $this->data['Document']['filename']['error'];
			if($error == 1){
				$this->Session->setFlash(__('Max file upload size exceeded.', true));
			}
			else{
				$filename = $this->data['Document']['filename']['tmp_name'];
				$messages = $this->Tieredoriginationratemaster->import($filename, $customerid);
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
