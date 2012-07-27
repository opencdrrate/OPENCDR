<?php
class RatedCallsController extends AppController {

	var $name = 'RatedCalls';
	var $paginate = array(
		'limit' => 1000
	);
	var $uses = array('Callrecordmaster');
	function index() {
		$this->Callrecordmaster->recursive = 0;
		$this->set('data', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid callrecordmaster', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('callrecordmaster', $this->Callrecordmaster->read(null, $id));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for callrecordmaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Callrecordmaster->delete($id)) {
			$this->Session->setFlash(__('Callrecordmaster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Callrecordmaster was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function tocsv(){
		$this->set('data', $this->Callrecordmaster->find('all'));
		$this->layout = '';
	}
	
	function topipe(){
		$this->set('data', $this->Callrecordmaster->find('all'));
		$this->layout = '';
	}
}
