<?php

class Retailplanterminationrate extends AppModel{
	var $name = 'Retailplanterminationrate';
	var $useTable = 'retailplanterminationratemaster';
	var $primaryKey = 'rowid';
	var $actsAs = array('ImportCsv');
	var $belongsTo = array(
		'RetailPlan' => array(
			'className' => 'Retailplanmaster',
			'foreignKey' => 'planid'
		)
	);

	function loadtype($line, $type = null){
		$data = str_getcsv($line);
		if(empty($data[3])){
			$canbefree = 0;
		}
		else{
			$canbefree = $data[3];
		}
		
		if(!is_numeric($canbefree)){
			return false;
		}
		
		if($canbefree == 1){
			$canbefree = true;
		}
		else{
			$canbefree = false;
		}
		$item['planid'] = $type;
		$item['effectivedate'] = $data[0];
		$item['billedprefix'] = $data[1];
		$item['retailrate'] = $data[2];
		$item['canbefree'] = $canbefree;
		
		return $item;
	}
}
?>