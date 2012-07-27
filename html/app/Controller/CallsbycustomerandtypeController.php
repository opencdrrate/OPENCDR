<?php
class CallsbycustomerandtypeController extends AppController {

	var $name = 'Callsbycustomerandtype';
	var $uses = array('Callrecordmaster','Siteconfiguration');
	var $paginate = array(
		'fields' => array('customerid', 'calltype', 'count(callid) AS "Calls"', 
			'sum(duration) / 60 as "Raw Duration"', 'sum(billedduration) / 60 as "Billed Duration"',
			'sum(lrndipfee) as "LRN Fees"', 'sum(cnamfee) as "CNAM Fees"', 
			'sum(retailprice - lrndipfee - cnamfee) as "Usage Fees"',
			'sum(retailprice) as "Total Fees"'),
		'group' => array('Callrecordmaster.customerid', 'Callrecordmaster.calltype'),
		'limit' => 20
	);
	function index() {
		$this->Callrecordmaster->recursive = 0;
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->set('data', $this->paginate());
	}

	function tocsv(){
		$this->set('data', $this->Callrecordmaster->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group']
												)));
		$this->layout = '';
	}
}