<?php
class BillingbatchesController extends AppController {

	var $name = 'Billingbatches';
	var $uses = array('Billingbatchmaster', 'Customer');
	function index() {
		$this->Billingbatchmaster->recursive = 0;
		$this->set('billingbatchmasters', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid billingbatchmaster', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('billingbatchmaster', $this->Billingbatchmaster->read(null, $id));
	}

	function add() {
	$billingcycles = $this->GetBillingCycles();
	ksort($billingcycles);
		$this->set('Billingcycles', $billingcycles);
		if (!empty($this->data)) {
			$count = $this->Billingbatchmaster->find( 'count', array(
				'conditions' => array(
					'billingbatchid' => $this->data['Billingbatchmaster']['billingbatchid']
					), 'recursive' => -1
				)
			);
			if(empty($this->data['Billingbatchmaster']['billingbatchid'])){
				$this->Session->setFlash(__('ERROR: empty Batch ID', true));
				$this->redirect(array('action' => 'index'));
			}
			if($count > 0){
				$this->Session->setFlash(__('ERROR: Batch ID already exists', true));
				$this->redirect(array('action' => 'index'));
			}
			else{
				if ($this->Billingbatchmaster->GenerateBillingBatch($this->data)) {
					$this->Session->setFlash(__('Batch successfully generated.', true));
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('Sorry, could not generate batch.', true));
				}
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for billingbatchmaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Billingbatchmaster->DeleteBillingBatch($id)) {
			$this->Session->setFlash(__('Billingbatchmaster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Billingbatchmaster was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function tocsv(){
		$this->set('data', $this->Billingbatchmaster->find('all'));
		$this->layout = '';
	}
	
	private function GetBillingCycles(){
		$billingcycles = array();
		$customers = $this->Customer->find('all',
				array(
					'fields' => array('distinct(Customer.billingcycle)')
				)
			);
		foreach($customers as $customer){
			$billingcycles[$customer[0]['billingcycle']] = $customer[0]['billingcycle'];
		}
		return $billingcycles;
	}
}
