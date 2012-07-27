<?php
class PaymentsController extends AppController {

	var $name = 'Payments';
	var $uses = array('Paymentmaster');
	

	function add($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid page', true));
				$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index')
				);
		}
		else{
			$customers = $this->Paymentmaster->Customer->find('list');
			$this->set(compact('customers'));
			$this->set('customerid', $id);
		}
		if (!empty($this->data)) {
			$this->Paymentmaster->create();
			if ($this->Paymentmaster->save($this->data)) {
				$this->Session->setFlash(__('Payment saved', true));
				$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
			} else {
				$this->Session->setFlash(__('Payment not saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid paymentmaster', true));
			$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Paymentmaster->save($this->data)) {
				$this->Session->setFlash(__('The paymentmaster has been saved', true));
				$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
			} else {
				$this->Session->setFlash(__('The paymentmaster could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Paymentmaster->read(null, $id);
		}
		$customers = $this->Paymentmaster->Customer->find('list');
		$this->set(compact('customers'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for paymentmaster', true));
			$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
		}
		if ($this->Paymentmaster->delete($id)) {
			$this->Session->setFlash(__('Paymentmaster deleted', true));
			$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
		}
		$this->Session->setFlash(__('Paymentmaster was not deleted', true));
		$this->redirect(array(
					'controller' => 'customers',
					'action' => 'index'));
	}
}
