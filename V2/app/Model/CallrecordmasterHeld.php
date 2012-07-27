<?php
class CallrecordmasterHeld extends AppModel {
	var $name = 'CallrecordmasterHeld';
	var $useTable = 'callrecordmaster_held';
	var $primaryKey = 'rowid';
	
	function moveToTBR(){
		$moveString = 'SELECT "fnMoveHELDCDRToTBR"();';
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->rawQuery($moveString);
	}
	
	function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val['CallrecordmasterHeld']['calltype'])) {
				$case = $val['CallrecordmasterHeld']['calltype'];
				
				$results[$key]['CallrecordmasterHeld']['calltype'] = $this->calltype($case);
			}
		}
	
		return $results;
	}
}
