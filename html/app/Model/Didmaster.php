<?php
class Didmaster extends AppModel {
	var $name = 'Didmaster';
	var $useTable = 'didmaster';
	var $primaryKey = 'rowid';
	var $actsAs = array('ImportCsv');
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	
	function loadtype($line, $type = null){
		$data = str_getcsv($line);
		$item['did'] = $data[0];
		$item['customerid'] = $data[1];
		$item['sipusername'] = $data[2];
		$item['sippassword'] = $data[3];
		if($data[4] == 'active'){
			$item['sipstatus']= 1;
		}
		else if($data[4] == 'inactive'){
			$item['sipstatus']= 0;
		}
		else{
			echo 'empty<br>';
			return array();
		}
		print_r($item);
		return $item;
	}
	
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
