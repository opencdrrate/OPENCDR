<?php
class ContactinformationController extends AppController{
	var $name = 'Contactinformation';
	
	function index() {
		$this->Contactinformation->recursive = 0;
		$this->set('ContactInformation', $this->paginate());
	}
	function add($customerid = null) {
		
		if (!empty($this->data)) {
			
			$this->Contactinformation->create();
			if ($this->Customerbillingaddressmaster->save($this->data)) {
				$this->Session->setFlash(__('Contact information has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Contact information could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid contact information', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Contactinformation->save($this->data)) {
				$this->Session->setFlash(__('Contact information has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Contact information could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Contactinformation->read(null, $id);
		}
	}
}
?>