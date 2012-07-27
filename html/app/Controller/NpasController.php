<?php
class NpasController extends AppController {

	var $name = 'Npas';
	var $uses = 'Npamaster';
	var $paginate = array('limit'=>100);
	function index() {
		$this->Npamaster->recursive = 0;
		$this->set('npamasters', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid npamaster', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('npamaster', $this->Npamaster->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Npamaster->create();
			if ($this->Npamaster->save($this->data)) {
				$this->Session->setFlash(__('The npamaster has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The npamaster could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid npamaster', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Npamaster->save($this->data)) {
				$this->Session->setFlash(__('The npamaster has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The npamaster could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Npamaster->read(null, $id);
			$this->set('npamaster', $this->data);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for npamaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Npamaster->delete($id)) {
			$this->Session->setFlash(__('Npamaster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Npamaster was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function import(){
		if (!empty($this->data)) {

			$filename = $this->data['Document']['filename']['tmp_name'];
			$type = '';
			$messages = $this->Npamaster->import($filename, $type);
			$this->Session->setFlash(__($messages, true));
			$this->redirect(array('action' => 'index'));
		}
	}
	function tocsv(){
		$this->set('data', $this->Npamaster->find('all'));
		$this->layout = '';
	}
}
