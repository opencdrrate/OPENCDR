<?php
class BillingbatchdetailsController extends AppController {

	var $name = 'Billingbatchdetails';

	function index($billingbatchid = null, $customerid = null) {
		if( !$billingbatchid || !$customerid){
			$this->Session->setFlash(__('Invalid page', true));
			$this->redirect('/');
		}
		$this->Billingbatchdetail->recursive = 0;
		$this->paginate = array(
						'conditions' => array(
						'Billingbatchdetail.billingbatchid' => $billingbatchid,
						'Billingbatchdetail.customerid' => $customerid
						)
					);
		$this->set('billingbatchid', $billingbatchid);
		$this->set('billingbatchdetails', $this->paginate());
	}

	function add() {
		if (!empty($this->data)) {
			$this->Billingbatchdetail->create();
			if ($this->Billingbatchdetail->save($this->data)) {
				$this->Session->setFlash(__('The billingbatchdetail has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The billingbatchdetail could not be saved. Please, try again.', true));
			}
		}
		$customers = $this->Billingbatchdetail->Customer->find('list');
		$billingBatches = $this->Billingbatchdetail->BillingBatch->find('list');
		$this->set(compact('customers', 'billingBatches'));
	}

	function edit($id = null) {
		$this->set('customers', $this->Billingbatchdetail->Customer->find('list'));
		$this->set('billingbatchmasters', $this->Billingbatchdetail->BillingBatch->find('list'));
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid billingbatchdetail', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Billingbatchdetail->save($this->data)) {
				$this->Session->setFlash(__('The billingbatchdetail has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The billingbatchdetail could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Billingbatchdetail->read(null, $id);
		}
		$customers = $this->Billingbatchdetail->Customer->find('list');
		$billingBatches = $this->Billingbatchdetail->BillingBatch->find('list');
		$this->set(compact('customers', 'billingBatches'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for billingbatchdetail', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Billingbatchdetail->delete($id)) {
			$this->Session->setFlash(__('Billingbatchdetail deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Billingbatchdetail was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
