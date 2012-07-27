<?php
class RetailplanterminationrateController extends AppController{
	var $name = 'Retailplanterminationrate';
	var $uses = array('Retailplanterminationrate');
	
	public function index($planid = null){
		$this->Retailplanterminationrate->recursive = 0;
		if(!empty($planid)){
			$this->paginate = array('conditions'=>array('Retailplanterminationrate.planid' => $planid));
			$this->set('planid', $planid);
		}
		$this->set('retailplanterminationrates', $this->paginate());
	}
	public function import($planid = null){
		$this->set('planid', $planid);
		if (!empty($this->data)) {
				$planid = $this->data['Retailplanterminationrate']['planid'];
				$error = $this->data['Document']['filename']['error'];
				if($error == 1){
					$this->Session->setFlash(__('Max file upload size exceeded.', true));
				}
				else{
					$filename = $this->data['Document']['filename']['tmp_name'];
					$messages = $this->Retailplanterminationrate->import($filename, $planid);
					$this->Session->setFlash(__($messages, true));
					$this->redirect(array('action' => 'index', $planid));
				}
			if(!$planid){
				$this->Session->setFlash(__('Invalid Page', true));
				$this->redirect('/');
			}
		}
	}
	public function tocsv($planid = null){
		if(!empty($planid)){
			$data = $this->Retailplanterminationrate->find('all', 
				array('conditions' => 
					array('planid' => $planid)));
			$this->set('data', $data);
		}
		else{
			$this->set('data', $this->Retailplanterminationrate->find('all'));
		}
		$this->layout = '';
	}
}
?>