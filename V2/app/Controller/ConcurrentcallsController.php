<?php
/*
"select 
		carrierid, 
		ratecenter, 
		cast(avg(\"Calls\") as bigint) as \"Calls\", 
		avg(\"RawDuration\") as \"RawDuration\", 
		avg(\"BilledDuration\") as \"BilledDuration\" 
			from vwcallspermonthpercarrierratecenter where direction = 'O'
			group by carrierid, ratecenter
			order by carrierid, ratecenter;";
*/
class ConcurrentcallsController extends AppController{
	var $name = 'Concurrentcalls';
	
	var $uses = array('Callspermonthpercarrierratecenter');
	var $paginate = array(
		'limit' => 20,
		'fields' => array(
			'carrierid',
			'ratecenter',
			'cast(avg("Calls") as bigint) as "Calls"',
			'avg("RawDuration") as "RawDuration"',
			'avg("BilledDuration") as "BilledDuration"'
		),
		'group' => array(
					'Callspermonthpercarrierratecenter.carrierid', 
					'Callspermonthpercarrierratecenter.ratecenter'
		),
		'conditions' => array(
			'direction' => 'O'
		)
		
	);
	
	
	
	function index() {
		$this->Callspermonthpercarrierratecenter->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Callspermonthpercarrierratecenter->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'],
													'conditions' => $this->paginate['conditions']
												)));
		$this->layout = '';
	}
}
?>