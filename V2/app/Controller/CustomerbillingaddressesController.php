<?php
class CustomerbillingaddressesController extends AppController {

	var $name = 'Customerbillingaddresses';
	var $uses = array('Customerbillingaddressmaster');

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid billing address', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('customerbillingaddressmaster', $this->Customerbillingaddressmaster->read(null, $id));
	}

	function add($id = null) {
		
		if (!empty($this->data)) {
			
			$this->Customerbillingaddressmaster->create();
			if ($this->Customerbillingaddressmaster->save($this->data)) {
				$this->Session->setFlash(__('Billing address has been saved', true));
				$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
			} else {
				$this->Session->setFlash(__('Billing address could not be saved. Please, try again.', true));
			}
		}
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid page', true));
			$this->redirect(
				array(
					'controller' => 'customers',
					'action' => 'index'
				)
			);
		}
		else{
			$customers = $this->Customerbillingaddressmaster->Customer->find('list');
			$this->set(compact('customers'));
			$this->set('customerid', $id);
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid billing address', true));
			$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
		}
		if (!empty($this->data)) {
			if ($this->Customerbillingaddressmaster->save($this->data)) {
				$this->Session->setFlash(__('The billing address has been saved', true));
				$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
			} else {
				$this->Session->setFlash(__('The billing address could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Customerbillingaddressmaster->read(null, $id);
		}
		$customers = $this->Customerbillingaddressmaster->Customer->find('list');
		$this->set(compact('customers'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for billing address', true));
			$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
		}
		if ($this->Customerbillingaddressmaster->delete($id)) {
			$this->Session->setFlash(__('Billing address deleted', true));
			$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
		}
		$this->Session->setFlash(__('Billing address was not deleted', true));
		$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
	}
}
