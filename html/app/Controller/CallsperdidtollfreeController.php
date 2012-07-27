<?php
/*
"select 
	originatingnumber, 
	count(*) as \"Calls\", 
	sum(duration)/ 60 as \"RawMinutes\", 
	sum(billedduration) / 60 as \"BilledMinutes\", 
	sum(retailprice) as \"RetailPrice\" 
		from callrecordmaster
        where calltype = 30
		 and calldatetime between '2000-01-01' and '2012-12-31'
		 group by originatingnumber
		 order by sum(retailprice) desc;";
*/
class CallsperdidtollfreeController extends AppController{
	var $name = 'Callsperdidtollfree';
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
			'calltype' => '30'
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