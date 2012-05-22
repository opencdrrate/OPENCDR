<?php
include_once 'AbstractFileImporter.php';
class SimpleterminationRateFileImporter extends AbstractFileImporter{
	private $customerid;
	function SimpleterminationRateFileImporter($table, $customerid){
		$this->customerid = $customerid;
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		$customerid = $this->customerid;
		list($effectivedate,$billedprefix,$retailrate) = $data;
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