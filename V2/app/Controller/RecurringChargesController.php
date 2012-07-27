<?php
class RecurringChargesController extends AppController {

	var $name = 'RecurringCharges';
	var $uses = array('Recurringchargemaster');
	function index($customerid = null) {
		$this->Recurringchargemaster->recursive = 0;
		if(!empty($customerid)){
			$this->paginate = array('conditions' => array('customerid' => $customerid));
			$this->set('customerid', $customerid);
		}
		else{
			$this->Session->setFlash(__('Invalid customerid', true));
			$this->redirect('/');
		}
		$this->set('recurringchargemasters', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid recurring charge', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('recurringchargemaster', $this->Recurringchargemaster->read(null, $id));
	}

	function add($customerid = null) {
		if (!$customerid && empty($this->data)) {
			$this->Session->setFlash(__('Invalid customerid', true));
			$this->redirect(array('/'));
		}
		else{
			$this->set('customerid', $customerid);
		}
		if (!empty($this->data)) {
			$this->Recurringchargemaster->create();
			if ($this->Recurringchargemaster->save($this->data)) {
				$this->Session->setFlash(__('Recurring charge saved', true));
				$this->redirect('/Wholesalerates');
			} else {
				$this->Session->setFlash(__('Recurring charge not saved. Please, try again.', true));
				$this->set('customerid', $this->data['Recurringchargemaster']['customerid']);
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid recurring charge', true));
			$this->redirect('/Wholesalerates');
		}
		if (!empty($this->data)) {
			if ($this->Recurringchargemaster->save($this->data)) {
				$this->Session->setFlash(__('Recurring charge saved', true));
				$this->redirect('/Wholesalerates');
			} else {
				$this->Session->setFlash(__('Recurring charge not saved. Please, try again.', true));
				$this->set('customerid', $this->data['Recurringchargemaster']['customerid']);
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Recurringchargemaster->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for recurring charge', true));
			$this->redirect('/Wholesalerates');
		}
		if ($this->Recurringchargemaster->delete($id)) {
			$this->Session->setFlash(__('Recurring charge deleted', true));
			$this->redirect('/Wholesalerates');
		}
		$this->Session->setFlash(__('Recurring charge was not deleted', true));
		$this->redirect('/Wholesalerates');
	}
	
	
	function tocsv($customerid = null){
		if(!empty($customerid)){
			$this->set('data', $this->Recurringchargemaster->find('all', 
				array('conditions' => 
					array('customerid' => $customerid))));
		}
		else{
			$this->set('data', $this->Recurringchargemaster->find('all'));
		}
		$this->layout = '';
	}
}
