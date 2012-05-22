<?php
include_once 'AbstractFileImporter.php';
class TieredoriginationRateFileImporter extends AbstractFileImporter{
	private $customerid;
	function TieredoriginationRateFileImporter($table, $customerid){
		$this->customerid = $customerid;
		$this->sqlTable = $table;
	}
	function ParseLine($data){
		$customerid = $this->customerid;
		list($effectivedate,$tier,$retailrate) = $data;
				$oldParams = array('customerid'=>$customerid,
									'effectivedate'=>$effectivedate,
									'tier'=>$tier);
				$newParams = array('customerid'=>$customerid, 
									'effectivedate'=>$effectivedate,
									'tier'=>$tier, 
									'retailrate'=>$retailrate);
		return array('oldParams' => $oldParams, 'newParams'=>$newParams);
	}
}
?>