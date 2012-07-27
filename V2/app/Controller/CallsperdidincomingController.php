<?php
/*
""select 
	originatingnumber, 
	count(*) as \"Calls\", 
	sum(duration)/ 60 as \"RawMinutes\", 
	sum(billedduration) / 60 as \"BilledMinutes\", 
	sum(retailprice) as \"RetailPrice\" from callrecordmaster
         where direction = 'I'
		 and calldatetime between '2000-01-01' and '2012-12-31'
		 group by originatingnumber
		 order by sum(retailprice) desc;";
*/
class CallsperdidincomingController extends AppController{
	var $name = 'Callsperdidincoming';
	var $uses = array('Callrecordmaster');
	
	var $paginate = array(
		'fields' => array(
			'originatingnumber',
			'count(*) as "Calls"',
			'sum(duration)/ 60 as "RawMinutes"',
			'sum(billedduration) / 60 as "BilledMinutes"',
			'sum(retailprice) as "RetailPrice"',
		),
		'conditions' => array(
			'direction' => 'I'
		),
		'group' => array(
			'Callrecordmaster.originatingnumber'
		),
		'limit' => 20
	);
	
	function index() {
		$this->Callrecordmaster->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Callrecordmaster->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'],
													'conditions' => $this->paginate['conditions']
												)));
		$this->layout = '';
	}
}
?>