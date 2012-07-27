<?php
/*
"select customerid, carrierid, date_part('year', calldatetime) as \"Year\", date_part('month', calldatetime) as \"Month\", 
		sum(\"InboundCall\") as \"InboundCalls\", sum(\"InboundDuration\") / 60 as \"InboundRawDuration\", sum(\"InboundBilledDuration\") / 60 as \"InboundBilledDuration\", 
		 sum(\"OutboundCall\") as \"OutboundCalls\", sum(\"OutboundDuration\") / 60 as \"OutboundRawDuration\", sum(\"OutboundBilledDuration\") / 60 as \"OutboundBilledDuration\" from vwcalldirection 
		 group by customerid, carrierid, date_part('year', calldatetime), date_part('month', calldatetime)
		 order by customerid, carrierid, date_part('year', calldatetime), date_part('month', calldatetime);";
	
*/
class InboundvsoutboundreportController extends AppController {

	var $name = 'Inboundvsoutboundreport';
	var $uses = array('Calldirection');
	var $paginate = array(
		'fields' => array('customerid', 'carrierid', 'date_part(\'year\', calldatetime) as "Year"', 
			'date_part(\'month\', calldatetime) as "Month"',
			'sum("InboundCall") as "InboundCalls"',
			'sum("InboundDuration") / 60 as "InboundRawDuration"',
			'sum("InboundBilledDuration") / 60 as "InboundBilledDuration"',
			'sum("OutboundCall") as "OutboundCalls"',
			'sum("OutboundDuration") / 60 as "OutboundRawDuration"',
			'sum("OutboundBilledDuration") / 60 as "OutboundBilledDuration"'
		),
			
		'group' => array('Calldirection.customerid', 'Calldirection.carrierid',
						'date_part(\'year\', Calldirection.calldatetime)',
						'date_part(\'month\', Calldirection.calldatetime)'),
		'limit' => 20
	);
	function index() {
		$this->Calldirection->recursive = 0;
		$this->set('data', $this->paginate());
	}

	function tocsv(){
		$this->set('data', $this->Calldirection->find('all', array('fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'])));
		$this->layout = '';
	}
}