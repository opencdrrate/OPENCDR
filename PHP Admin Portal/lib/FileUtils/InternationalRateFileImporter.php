<?php
include_once 'AbstractFileImporter.php';
class InternationalRateFileImporter extends AbstractFileImporter{
	private $customerid;
	function InternationalRateFileImporter($table, $customerid){
		$this->customerid = $customerid;
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		list($effectivedate,$billedprefix,$retailrate) = $data;
		$customerid = $this->customerid;
		$oldParams = array('customerid'=>$customerid,
									'effectivedate'=>$effectivedate,
									'billedprefix'=>$billedprefix);
		$newParams = array('customerid'=>$customerid, 
									'effectivedate'=>$effectivedate,
									'billedprefix'=>$billedprefix, 
									'retailrate'=>$retailrate);	
		
		return array('oldParams' => $oldParams, 'newParams'=>$newParams);
	}
}
?>