<?php
class RetailplansController extends AppController {
	var $name = 'Retailplan';
	var $uses = array('Retailplanmaster', 'Siteconfiguration');
	
	function index(){
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->Retailplanmaster->recursive = 1;
		$this->set('retailplanmasters', $this->paginate());
	}
	
	function add(){
		if (!empty($this->data)) {
			$this->Retailplanmaster->create();
			if ($this->Retailplanmaster->save($this->data)) {
				$this->Session->setFlash(__('Retail plan added!', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Error creating retail plan', true));
			}
		}
	}
	
	function edit($id = null){
		if (!empty($this->data)) {
			if ($this->Retailplanmaster->save($this->data)) {
				$this->Session->setFlash(__('Changes saved.', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Changes not saved.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Retailplanmaster->read(null, $id);
		}
	}
	function delete($id=null){
		if ($this->Retailplanmaster->delete($id)) {
			$this->Session->setFlash(__('Retail plan removed.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Retail plan not removed.', true));
		$this->redirect(array('action' => 'index'));
	}
}