<?php
class Tollfreeoriginationratemaster extends AppModel {
	var $name = 'Tollfreeoriginationratemaster';
	var $useTable = 'tollfreeoriginationratemaster';
	var $primaryKey = 'rowid';
	var $actsAs = array('ImportCsv');
	var $validate = array(
		'effectivedate' =>
					array(
							'valid-date' => array(
								'rule' => 'anyDate',
								'required' => true,
								'allowEmpty' => false,
								'last' => true,
								'message' => 'Must be a valid date field'
							),
							'unique' => array(
								'rule'=>array('checkUnique', array('customerid', 'effectivedate','billedprefix')), 
								'message'=>'A Billed Prefix/Effective Date pair already exists for that customer'
							)
					)
	);
	
	function loadtype($line, $type = null){
		$data = str_getcsv($line);
		if($type == null){
			$item['customerid'] = $data[0];
			$item['billedprefix'] = $data[1];
			$item['effectivedate'] = $data[2];
			$item['retailrate'] = $data[3];
		}
		else{
			$item['customerid'] = $type;
			$item['billedprefix'] = $data[0];
			$item['effectivedate'] = $data[1];
			$item['retailrate'] = $data[2];
		}
		
		return $item;
	}
}
