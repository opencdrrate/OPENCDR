<?php
class CustomerretailplansController extends AppController {

	var $name = 'Customerretailplans';
	var $uses = array('Customerretailplanmaster', 'Retailplanmaster');
	function index($id=null) {
		$this->Customerretailplanmaster->recursive = 1;
		if($id){
			$this->paginate = array(
				'Customerretailplanmaster' => array(
					'conditions' => array('Customerretailplanmaster.planid'=>$id)
				)
			);
			$this->set('customerretailplanmasters', $this->paginate());
		}
		else{
			$this->set('customerretailplanmasters', $this->paginate());
		}
	}

	function add() {
		$this->set('customers', $this->Customerretailplanmaster->Customer->find('list', array('conditions'=>array('customertype'=>'Retail'))));
		$this->set('retailplans', $this->Retailplanmaster->find('list', array('fields'=>array('Retailplanmaster.planid','Retailplanmaster.planid'))));
		if (!empty($this->data)) {
			$this->Customerretailplanmaster->create();
			if ($this->Customerretailplanmaster->save($this->data)) {
				$this->Session->setFlash(__('Saved', true));
				$this->redirect(array('controller' => 'Retailplans','action' => 'index'));
			} else {
				$this->Session->setFlash(__('Not saved. Please, try again.', true));
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Customerretailplanmaster->delete($id)) {
			$this->Session->setFlash(__('Deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
