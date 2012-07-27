<?php
class SipcredentialsController extends AppController {
	var $name = 'Sipcredentials';
	var $uses = array('Sipcredential','Vwsipcredential');
	
	function add($customerid = null){
		if (!empty($this->data)) {
			$customerid = $this->data['Sipcredential']['customerid'];
			$this->Sipcredential->create();
			if ($this->Sipcredential->save($this->data)) {
				$this->Session->setFlash(__('SIP credential saved', true));
				$this->redirect(array('controller' => 'customers','action' => 'view',$customerid));
			} else {
				$this->Session->setFlash(__('SIP credential could not be saved. Please, try again.', true));
			}
		}
		
		if(empty($customerid)){
			$this->Session->setFlash(__('Invalid Page', true));
			$this->redirect(array('controller' => 'customers','action' => 'index'));
		}
		$this->set('customerid', $customerid);
	}
	
	function edit($customerid = null){
		if (!empty($this->data)) {
			$customerid = $this->data['Sipcredential']['customerid'];
			if ($this->Sipcredential->save($this->data)) {
				$this->Session->setFlash(__('SIP credential saved', true));
				$this->redirect(array('controller' => 'customers','action' => 'view',$customerid));
			} else {
				$this->Session->setFlash(__('SIP credential could not be saved. Please, try again.', true));
			}
		}
		else{
			$this->data = $this->Sipcredential->read(null, $customerid);
		}
		
		if(empty($customerid)){
			$this->Session->setFlash(__('Invalid Page', true));
			$this->redirect(array('controller' => 'customers','action' => 'index'));
		}
		$this->set('customerid', $customerid);
	}
	function csv(){
		$this->set('data', $this->Vwsipcredential->find('all'));
		$this->layout='';
	}
}
?>