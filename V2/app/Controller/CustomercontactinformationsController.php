<?php
class CustomercontactinformationsController extends AppController {

	var $name = 'Customercontactinformation';
	var $uses = array('Contactinformation');
	
	function add($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid contact information', true));
			$this->redirect(
				array(
					'controller' => 'customers',
					'action' => 'index'
				)
			);
		}
		else{
			$this->set('customerid', $id);
		}
		if (empty($this->data)) {
			$this->data = $this->Contactinformation->read(null, $id);
		}
		
		if (!empty($this->data)) {
	
			$this->Contactinformation->create();
			if ($this->Contactinformation->save($this->data)) {
				$this->Session->setFlash(__('Contact information has been saved', true));
				$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
			} else {
				$this->Session->setFlash(__('Contact information could not be saved. Please, try again.', true));
			}
		}
	}
	
	function edit($id = null){
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid contact information', true));
			$this->redirect(
				array(
					'controller' => 'customers',
					'action' => 'index'
				)
			);
		}
		if (!empty($this->data)) {
			if ($this->Contactinformation->save($this->data)) {
				$this->Session->setFlash(__('Contact information has been saved', true));
				$this->redirect(
					array(
						'controller' => 'customers',
						'action' => 'index'
					)
				);
			} else {
				print_r($this->data);
				$this->Session->setFlash(__('Contact information could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Contactinformation->read(null, $id);
		}
	}
}
?>