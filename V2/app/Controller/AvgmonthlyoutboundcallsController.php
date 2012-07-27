<?php
/*
 "select carrierid, cast(avg(\"Calls\") as bigint) as \"Calls\", 
	avg(\"RawDuration\") as \"RawDuration\", 
	avg(\"BilledDuration\") as \"BilledDuration\" from vwcallspermonthpercarrier where direction = 'I'
		 group by carrierid
		 order by carrierid;";
*/
class AvgmonthlyoutboundcallsController extends AppController{
	var $name = 'Avgmonthlyoutboundcalls';
	var $uses = array('Callspermonthpercarrier');
	var $paginate = array(
		'fields' => array(
						'carrierid',
						'cast(avg("Calls") as bigint) as "Calls"',
						'avg("RawDuration") as "RawDuration"',
						'avg("BilledDuration") as "BilledDuration"'
		),
		'conditions' => array(
			'direction' => 'O'
		),
		'group' => array('Callspermonthpercarrier.carrierid'
		),
		'limit' => 20
	);
	function index() {
		$this->Callspermonthpercarrier->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Callspermonthpercarrier->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'],
													'conditions' => $this->paginate['conditions']
												)));
		$this->layout = '';
	}
}
?>