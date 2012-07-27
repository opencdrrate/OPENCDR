<?php
class RateCentersController extends AppController {

	var $name = 'RateCenters';
	var $uses = 'Tieredoriginationratecentermaster';
	function index() {
		$this->Tieredoriginationratecentermaster->recursive = 0;
		$this->set('tieredoriginationratecentermasters', $this->paginate());
	}

	function add() {
		if (!empty($this->data)) {
			$this->Tieredoriginationratecentermaster->create();
			if ($this->Tieredoriginationratecentermaster->save($this->data)) {
				$this->Session->setFlash(__('Data saved.', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Rate center could not be saved.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid ID.', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Tieredoriginationratecentermaster->save($this->data)) {
				$this->Session->setFlash(__('Rate center saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Rate center could not be saved.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Tieredoriginationratecentermaster->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Tieredoriginationratecentermaster->delete($id)) {
			$this->Session->setFlash(__('Error deleting entry', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Error deleting entry', true));
		$this->redirect(array('action' => 'index'));
	}
	function tocsv(){
		$this->set('data', $this->Tieredoriginationratecentermaster->find('all'));
		$this->layout = '';
	}
}
