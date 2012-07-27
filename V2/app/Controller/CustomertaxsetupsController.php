<?php
class CustomertaxsetupsController extends AppController {

	var $name = 'Customertaxsetups';

	function index() {
		$this->Customertaxsetup->recursive = 0;
		$this->set('customertaxsetups', $this->paginate());
	}

	function add() {
		if (!empty($this->data)) {
			$this->Customertaxsetup->create();
			if ($this->Customertaxsetup->save($this->data)) {
				$this->Session->setFlash(__('The customertaxsetup has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->set('customers', $this->Customertaxsetup->Customer->find('list'));
				$this->set('calltypes', $this->GetCallTypes());
				$this->Session->setFlash(__('The customertaxsetup could not be saved. Please, try again.', true));
			}
		}
		else{
			$this->set('customers', $this->Customertaxsetup->Customer->find('list'));
			$this->set('calltypes', $this->GetCallTypes());
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid customertaxsetup', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Customertaxsetup->save($this->data)) {
				$this->Session->setFlash(__('Tax setup saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->set('customers', $this->Customertaxsetup->Customer->find('list'));
				$this->set('calltypes', $this->GetCallTypes());
				$this->Session->setFlash(__('Tax setup could not be saved. Please, try again.', true));
				$this->redirect(array('action' => 'index'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Customertaxsetup->read(null, $id);
			$this->set('customertaxsetup', $this->data);
			$this->set('customers', $this->Customertaxsetup->Customer->find('list'));
			$this->set('calltypes', $this->GetCallTypes());
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for customertaxsetup', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Customertaxsetup->delete($id)) {
			$this->Session->setFlash(__('Customertaxsetup deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Customertaxsetup was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	
	function tocsv(){
		$this->set('data', $this->Customertaxsetup->find('all'));
		$this->layout = '';
	}
	
	private function GetCallTypes(){
		$calltypes = array();
		$calltypes['5'] = 'Intrastate';
		$calltypes['10'] = 'Interstate';
		$calltypes['15'] = 'Tiered Origination';
		$calltypes['20'] = 'Termination of Indeterminate Jurisdiction';
		$calltypes['25'] = 'International';
		$calltypes['30'] = 'Toll-free Origination';
		$calltypes['35'] = 'Simple Termination';
		$calltypes['40'] = 'Toll-free Termination';
		return $calltypes;
	}
}
