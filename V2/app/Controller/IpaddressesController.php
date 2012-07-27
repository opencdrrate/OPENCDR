<?php
class IpaddressesController extends AppController {

	var $name = 'Ipaddresses';
	var $uses = array('Ipaddressmaster');
	function index() {
		$this->Ipaddressmaster->recursive = 0;
		$this->set('ipaddressmasters', $this->paginate());
	}

	function add() {
		$this->set('customers', $this->Ipaddressmaster->Customer->find('list'));
		if (!empty($this->data)) {
			$this->Ipaddressmaster->create();
			if ($this->Ipaddressmaster->save($this->data)) {
				$this->Session->setFlash(__('The IP address has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The IP address could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		$this->set('customers', $this->Ipaddressmaster->Customer->find('list'));
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid IP address', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Ipaddressmaster->save($this->data)) {
				$this->Session->setFlash(__('The IP address has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The IP address could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Ipaddressmaster->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for IP address', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Ipaddressmaster->delete($id)) {
			$this->Session->setFlash(__('IP address deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('IP address was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function tocsv(){
		$this->set('data', $this->Ipaddressmaster->find('all'));
		$this->layout = '';
	}
}
