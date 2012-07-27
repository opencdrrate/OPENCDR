<?php

/*
"select 
		cast(calldatetime as date) as \"Date\", 
		ratecenter, 
		direction, 
		max(concurrentcalls) as \"Peak\", 
		avg(concurrentcalls) as \"Average\" 
				from concurrentcallsdirectionratecenter
                 where ConcurrentCalls > 0
                 group by cast(calldatetime as date), ratecenter, direction
                 order by cast(calldatetime as date), ratecenter, direction;";
*/
class ConcurrentcallsratecenterreportController extends AppController {

	var $name = 'Concurrentcallsratecenterreport';
	var $uses = array('Concurrentcallsdirectionratecenter');
	var $paginate = array(
		'limit' => 20,
		'fields' => array(
			'cast(calldatetime as date) as "Date"',
			'ratecenter',
			'direction',
			'max(concurrentcalls) as "Peak"',
			'avg(concurrentcalls) as "Average"',
		),
		'group' => array(
			'Concurrentcallsdirectionratecenter.calldatetime',
			'Concurrentcallsdirectionratecenter.ratecenter',
			'Concurrentcallsdirectionratecenter.direction'
		),
		'conditions' => array('concurrentcalls > 0')
	);
	
	
	function index() {
		$this->Concurrentcallsdirectionratecenter->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Concurrentcallsdirectionratecenter->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'],
													'conditions' => $this->paginate['conditions']
												)));
		$this->layout = '';
	}
}
?>