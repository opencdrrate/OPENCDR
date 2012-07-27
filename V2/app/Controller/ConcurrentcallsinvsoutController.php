<?php

/*
"select 
	cast(calldatetime as date) as \"Date\", 
	max(concurrentcalls) as \"Peak\", 
	avg(concurrentcalls) as \"Average\" from concurrentcalls
                 group by cast(calldatetime as date)
                 order by cast(calldatetime as date);"
*/
class ConcurrentcallsinvsoutController extends AppController {

	var $name = 'Concurrentcallsinvsout';
	
	var $uses = array('Concurrentcalls');
	var $paginate = array(
		'limit' => 20,
		'fields' => array(
			'cast(calldatetime as date) as "Date"',
			'max(concurrentcalls) as "Peak"',
			'avg(concurrentcalls) as "Average"'
		),
		'group' => array('Concurrentcalls.calldatetime')
		
	);
	
	
	function index() {
		$this->Concurrentcalls->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Concurrentcalls->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group']
												)));
		$this->layout = '';
	}
}
?>