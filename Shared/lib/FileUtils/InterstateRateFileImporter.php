<?php
include_once 'AbstractFileImporter.php';
class InterstateRateFileImporter extends AbstractFileImporter{
	private $customerid;
	function InterstateRateFileImporter($table, $customerid){
		$this->customerid = $customerid;
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		list($effectivedate,$npanxxx,$retailrate) = $data;
		$customerid = $this->customerid;
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