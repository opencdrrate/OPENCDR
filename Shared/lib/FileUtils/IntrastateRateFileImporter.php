<?php
include_once 'AbstractFileImporter.php';
class IntrastateRateFileImporter extends AbstractFileImporter{
	private $customerid;
	function IntrastateRateFileImporter($table, $customerid){
		$this->customerid = $customerid;
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		$customerid = $this->customerid;
		list($effectivedate,$npanxxx,$retailrate) = $data;
				$oldParams = array('customerid'=>$customerid,
									'effectivedate'=>$effectivedate,
									'npanxxx'=>$npanxxx);
				$newParams = array('customerid'=>$customerid, 
									'effectivedate'=>$effectivedate,
									'npanxxx'=>$npanxxx, 
									'retailrate'=>$retailrate);
		
		return array('oldParams' => $oldParams, 'newParams'=>$newParams);
	}
}
?>