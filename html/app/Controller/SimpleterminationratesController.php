<?php
class SimpleterminationratesController extends AppController {

	var $name = 'Simpleterminationrates';
	var $uses = array('Simpleterminationratemaster');
	
	function index($customerid = null) {
		$this->Simpleterminationratemaster->recursive = 0;
		if(!empty($customerid)){
			$this->paginate=array('conditions' => array('customerid' => $customerid));
			$this->set('customerid', $customerid);
		}
		$this->set('simpleterminationratemasters', $this->paginate());
	}

	function delete($id = null,$customerid = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/Wholesalerates');
		}
		if (!$customerid) {
			$this->Session->setFlash(__('Invalid action', true));
			$this->redirect('/Wholesalerates');
		}
		if ($this->Simpleterminationratemaster->delete($id)) {
			$this->Session->setFlash(__('Item deleted', true));
				$this->redirect(array('action' => 'index', $customerid));
		}
		$this->Session->setFlash(__('Item not deleted', true));
				$this->redirect(array('action' => 'index', $customerid));
	}
	function tocsv($customerid = null){
		if(!empty($customerid)){
			$this->set('data', $this->Simpleterminationratemaster->find('all', 
				array('conditions' => 
					array('customerid' => $customerid))));
		}
		else{
			$this->set('data', $this->Simpleterminationratemaster->find('all'));
		}
		$this->layout = '';
	}
	
	function import($customerid = null){
		$this->set('customerid', $customerid);
		if (!empty($this->data)) {
			$customerid = $this->data['Simpleterminationratemaster']['customerid'];
			$error = $this->data['Document']['filename']['error'];
			if($error == 1){
				$this->Session->setFlash(__('Max file upload size exceeded.', true));
			}
			else{
				$filename = $this->data['Document']['filename']['tmp_name'];
				$messages = $this->Simpleterminationratemaster->import($filename, $customerid);
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
