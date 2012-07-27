<?php
class OnetimechargequeuesController extends AppController {

	var $name = 'Onetimechargequeues';
	private function InvalidIDError($message = 'Invalid id'){
		$this->Session->setFlash($message);
		$this->redirect('/Wholesalerates');
	}
	function index($customerid = null) {
		$this->Onetimechargequeue->recursive = 0;
		if (!$customerid && empty($this->data)) {
			$this->InvalidIDError();
		}
		if(!empty($customerid)){
			$this->paginate=array('conditions' => array('customerid' => $customerid));
			$this->set('customerid', $customerid);
		}
		$this->set('onetimechargequeues', $this->paginate());
	}


	function add($id = null) {
		
		if (!$id && empty($this->data)) {
			$this->InvalidIDError();
		}
		else{
			$this->set('customerid', $id);
		}
		if (!empty($this->data)) {
			$this->Onetimechargequeue->create();
			if ($this->Onetimechargequeue->save($this->data)) {
				$this->Session->setFlash('One time charge successfully saved');
				$this->redirect(array('action'=>'index', $this->data['Onetimechargequeue']['customerid']));
			} 
			else {
				$this->Session->setFlash('One time charge could not be saved. Please, try again.');
				$this->set('customerid', $this->data['Onetimechargequeue']['customerid']);
			}
		}
		
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->InvalidIDError();
		}
		if (!empty($this->data)) {
			if ($this->Onetimechargequeue->save($this->data)) {
				$this->Session->setFlash('One time charge successfully saved');
				$this->redirect(array('action'=>'index', $this->data['Onetimechargequeue']['customerid']));
			} else {
				$this->Session->setFlash(__('One time charge could not be saved. Please, try again.', true));
				$this->set('customerid', $this->data['Onetimechargequeue']['customerid']);
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Onetimechargequeue->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->InvalidIDError();
		}
		if ($this->Onetimechargequeue->delete($id)) {
			$this->Session->setFlash('One time charge successfully deleted');
			$this->redirect(array('action'=>'index', $this->data['Onetimechargequeue']['customerid']));
		}
		else{
			$this->InvalidIDError('Error deleting one time charge');
		}
	}
	function tocsv($customerid = null){		
		if (!$customerid) {
			$this->InvalidIDError();
		}
		if(!empty($customerid)){
			$this->set('data', $this->Onetimechargequeue->find('all', 
				array('conditions' => 
					array('customerid' => $customerid))));
		}
		else{
			$this->set('data', $this->Onetimechargequeue->find('all'));
		}
		$this->layout = '';
	}
}
