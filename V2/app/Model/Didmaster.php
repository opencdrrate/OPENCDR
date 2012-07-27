<?php
class Didmaster extends AppModel {
	var $name = 'Didmaster';
	var $useTable = 'didmaster';
	var $primaryKey = 'rowid';
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	
	Function afterFind($results, $primary = false){
		foreach ($results as $key => $val) {
			if (isset($val['Didmaster']['sipstatus'])) {
				$case = $val['Didmaster']['sipstatus'];
				$new = $case;
				if($case == '0'){
					$new = 'inactive';
				}
				else if($case == '1'){
					$new = 'active';
				}
				else{
					$new = 'inactive';
				}
				
				$results[$key]['Didmaster']['sipstatus'] = $new;
			}
		}
	
		return $results;
	}
}
