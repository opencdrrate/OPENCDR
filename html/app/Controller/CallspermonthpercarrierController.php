<?php

class CallspermonthpercarrierController extends AppController{
	var $name = 'Callspermonthpercarrier';
	var $uses = array('Callspermonthpercarrier');
	function index() {
		$this->Callspermonthpercarrier->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Callspermonthpercarrier->find('all'));
		$this->layout = '';
	}
}
?>