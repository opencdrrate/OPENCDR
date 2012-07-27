<?php
/*
"select sourceip, 
		count(*) as \"Calls\", 
		sum(duration) / 60 as \"RawMinutes\", 
		sum(billedduration) / 60 as \"BilledMinutes\" , 
		sum(retailprice) as \"RetailPrice\" 
				from callrecordmaster
                 where calldatetime between '$start' and '$end'
                 group by sourceip
                 order by sourceip;"
*/

class CallsperipaddressController extends AppController{

	var $name = 'Callsperipaddress';
	var $uses = array('Callrecordmaster', 'Siteconfiguration');
	
	var $paginate = array(
		'fields' => array(
			'sourceip',
			'count(*) as "Calls"',
			'sum(duration)/ 60 as "RawMinutes"',
			'sum(billedduration) / 60 as "BilledMinutes"',
			'sum(retailprice) as "RetailPrice"'
		),
		'group' => array(
			'Callrecordmaster.sourceip'
		),
		'limit' => 100
	);
	
	function index() {
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->Callrecordmaster->recursive = 0;
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
?>