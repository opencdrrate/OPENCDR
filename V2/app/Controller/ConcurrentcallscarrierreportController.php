<?php

/*
"select 
	cast(calldatetime as date) as \"Date\", 
	carrierid, 
	direction, 
	max(concurrentcalls) as \"Peak\", 
	avg(concurrentcalls) as \"Average\" 
				from concurrentcallsdirectioncarrier
                 where concurrentcalls > 0
                 group by cast(calldatetime as date), carrierid, direction
                 order by cast(calldatetime as date), carrierid, direction;";
*/
class ConcurrentcallscarrierreportController extends AppController {

	var $name = 'Concurrentcallscarrierreport';
	var $uses = array('Concurrentcallsdirectioncarrier');
	var $paginate = array(
		'limit' => 20,
		'fields' => array(
			'cast(calldatetime as date) as "Date"',
			'carrierid',
			'direction',
			'max(concurrentcalls) as "Peak"',
			'avg(concurrentcalls) as "Average"',
		),
		'group' => array(
			'Concurrentcallsdirectioncarrier.calldatetime',
			'Concurrentcallsdirectioncarrier.carrierid',
			'Concurrentcallsdirectioncarrier.direction'
		),
		'conditions' => array('concurrentcalls > 0')
	);
	
	
	function index() {
		$this->Concurrentcallsdirectioncarrier->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Concurrentcallsdirectioncarrier->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'],
													'conditions' => $this->paginate['conditions']
												)));
		$this->layout = '';
	}
}
?>