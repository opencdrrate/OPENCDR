<?php
class CustomersController extends AppController {

	var $name = 'Customers';
	var $uses = array('Customer', 'Sipcredential', 'Siteconfiguration');
	function index() {
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->Customer->recursive = 1;
		$this->set('customers', $this->paginate());
	}

	function view($id = null) {
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		if (!$id) {
			$this->Session->setFlash(__('Invalid customer', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('customer', $this->Customer->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Customer->create();
			if ($this->Customer->save($this->data)) {
				$this->Session->setFlash(__('The customer has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The customer could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid customer', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Customer->save($this->data)) {
				$this->Session->setFlash(__('The customer has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The customer could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Customer->read(null, $id);
			$sipcredentials =$this->Sipcredential->read(null, $id);
			//$this->data['Sipcredential'] = $sipcredentials['Sipcredential'];
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for customer', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Customer->delete($id, true)) {
			$this->Session->setFlash(__('Customer deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Customer was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function number(){
		return $this->Customer->find('count');
	}
	
	function tocsv(){
		$this->set('data', $this->Customer->find('all'));
		$this->layout = '';
	}
}
