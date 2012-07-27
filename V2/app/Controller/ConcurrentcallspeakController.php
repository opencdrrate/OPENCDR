<?php
class ConcurrentcallspeakController extends AppController {

	var $name = 'Concurrentcallspeak';
	
	var $uses = array('Concurrentcallsdirection');
	var $paginate = array(
		'limit' => 20,
		'fields' => array(
			'cast(calldatetime as date) as "Date"',
			'direction',
			'max(concurrentcalls) as "Peak"',
			'avg(concurrentcalls) as "Average"'
		),
		'group' => array('Concurrentcallsdirection.calldatetime', 'Concurrentcallsdirection.direction')
		
	);
	
	
	function index() {
		$this->Concurrentcallsdirection->recursive = 0;
		$this->set('data', $this->paginate());
	}
	function tocsv(){
		$this->set('data', $this->Concurrentcallsdirection->find('all', array(
													'fields' => $this->paginate['fields'],
													'group' => $this->paginate['group']
												)));
		$this->layout = '';
	}
}
?>