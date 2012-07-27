<?php
class RatingErrorsController extends AppController {

	var $name = 'RatingErrors';
	var $paginate = array(
		'limit' => 1000,
		'order' => array('CallrecordmasterHeld.calldatetime' => 'desc')
	);
	var $uses = array('CallrecordmasterHeld');
	
	function index() {
		$this->CallrecordmasterHeld->recursive = 0;
		$this->set('callrecordmasterHelds', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid callrecordmaster held', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('callrecordmasterHeld', $this->CallrecordmasterHeld->read(null, $id));
	}
/*
	function add() {
		if (!empty($this->data)) {
			$this->CallrecordmasterHeld->create();
			if ($this->CallrecordmasterHeld->save($this->data)) {
				$this->Session->setFlash(__('The callrecordmaster held has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The callrecordmaster held could not be saved. Please, try again.', true));
			}
		}
	}
*/
/*
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid callrecordmaster held', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->CallrecordmasterHeld->save($this->data)) {
				$this->Session->setFlash(__('The callrecordmaster held has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The callrecordmaster held could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->CallrecordmasterHeld->read(null, $id);
		}
	}
*/
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for callrecordmaster held', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->CallrecordmasterHeld->delete($id)) {
			$this->Session->setFlash(__('Callrecordmaster held deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Callrecordmaster held was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function moveToTBR(){
		$this->CallrecordmasterHeld->moveToTBR();
		$this->Session->setFlash(__('Records moved to rating queue', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function tocsv(){
		$this->set('callrecordmasterHelds', $this->CallrecordmasterHeld->find('all'));
		$this->layout = '';
	}
}
