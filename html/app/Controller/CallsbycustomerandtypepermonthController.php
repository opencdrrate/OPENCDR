<?php
/*
"select customerid, date_part('year', calldatetime) as \"Year\", 
	date_part('month', calldatetime) as \"Month\", 
	calltype, count(callid) as \"Calls\", sum(duration) / 60 as \"Raw Duration\", 
	sum(billedduration) / 60 as \"Billed Duration\", 
	sum(lrndipfee) as \"LRN Fees\", sum(cnamfee) as \"CNAM Fees\",
	sum(retailPrice - lrndipfee - cnamfee) as \"Usage Fees\", 
	sum(retailprice) as \"Total Fees\" 
		from callrecordmaster group by customerid, date_part('year', calldatetime), date_part('month', calldatetime),
        calltype order by customerid, date_part('year', calldatetime), date_part('month', calldatetime), calltype;";
	
*/
class CallsbycustomerandtypepermonthController extends AppController {

	var $name = 'Callsbycustomerandtypepermonth';
	var $uses = array('Callrecordmaster','Siteconfiguration');
	var $paginate = array(
		'fields' => array('customerid', 'date_part(\'year\', calldatetime) as "Year"', 
			'date_part(\'month\', calldatetime) as "Month"',
			'calltype', 'count(callid) AS "Calls"', 
			'sum(duration) / 60 as "Raw Duration"', 'sum(billedduration) / 60 as "Billed Duration"',
			'sum(lrndipfee) as "LRN Fees"', 'sum(cnamfee) as "CNAM Fees"', 
			'sum(retailprice - lrndipfee - cnamfee) as "Usage Fees"',
			'sum(retailprice) as "Total Fees"'),
		'group' => array('Callrecordmaster.customerid', 'Callrecordmaster.calltype',
						'date_part(\'year\', Callrecordmaster.calldatetime)',
						'date_part(\'month\', Callrecordmaster.calldatetime)'),
		'limit' => 20);
	function index() {
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->Callrecordmaster->recursive = 0;
		$this->set('data', $this->paginate());
	}

	function tocsv(){
		$this->set('data', $this->Callrecordmaster->find('all', array('fields' => $this->paginate['fields'],
													'group' => $this->paginate['group'])));
		$this->layout = '';
	}
}