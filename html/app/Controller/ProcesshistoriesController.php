<?php
class ProcesshistoriesController extends AppController {

	var $name = 'Processhistories';
	var $paginate = 
		array('order' => array(
			'Processhistory.startdatetime' => 'desc'
		)
	);
	function index() {
		$this->Processhistory->recursive = 0;
		$this->set('processhistories', $this->paginate());
	}

	function tocsv(){
		$this->set('data', $this->Processhistory->find('all'));
		$this->layout = '';
	}
}
