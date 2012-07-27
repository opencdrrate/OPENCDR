<?php
/*
"select 
		customerid, 
		calltype, 
		count(*) as \"Calls\", 
		sum(duration) / 60 as \"RawMinutes\", 
		sum(billedduration) / 60 as \"BilledMinutes\", 
		sum(retailprice) as \"RetailPrice\" 
			from callrecordmaster
            where calldatetime between '2000-01-01' and '2012-12-31'
            group by customerid, calltype
            order by customerid, calltype;";
*/

class CallspercustomerandtypeController extends AppController{

	var $name = 'Callspercustomerandtype';
	var $uses = array('Callrecordmaster', 'Siteconfiguration');
	
	var $paginate = array(
		'fields' => array(
			'customerid',
			'calltype',
			'count(*) as "Calls"',
			'sum(duration)/ 60 as "RawMinutes"',
			'sum(billedduration) / 60 as "BilledMinutes"',
			'sum(retailprice) as "RetailPrice"'
		),
		'group' => array(
			'Callrecordmaster.customerid',
			'Callrecordmaster.calltype'
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