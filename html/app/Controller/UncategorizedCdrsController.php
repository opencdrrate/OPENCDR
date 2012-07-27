<?php
class UncategorizedCdrsController extends AppController {

	var $name = 'UncategorizedCdrs';
	var $paginate = array(
		'conditions' => array('calltype IS NULL'),
		'limit' => 1000,
		'order' => array('UncategorizedCdr.calldatetime' => 'desc')
	);
	function index() {
		$this->UncategorizedCdr->recursive = 0;
		$this->set('uncategorizedCdrs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid CDR', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('uncategorizedCdr', $this->UncategorizedCdr->read(null, $id));
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for CDR', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->UncategorizedCdr->delete($id)) {
			$this->Session->setFlash(__('CDR deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('CDR was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function tocsv(){
		$this->set('data', $this->UncategorizedCdr->find('all'));
		$this->layout = '';
	}
	
	function categorizecdrs(){
		$this->UncategorizedCdr->categorizecdrs();
		$this->Session->setFlash(__('Categorization complete', true));
		$this->redirect(array('action' => 'index'));
	}
}
