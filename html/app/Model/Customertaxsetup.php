<?php
class Customertaxsetup extends AppModel {
	var $name = 'Customertaxsetup';
	var $useTable = 'customertaxsetup';
	var $primaryKey = 'rowid';
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	var $validate = array(
		'customerid' => array(
			'Required' => array(
				'rule' => array('maxLength', 15),
				'on'=>'create',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is required.'
				
			)
		),
		'calltype' => array(
			'Required' => array(
				'rule' => 'numeric',
				'on'=>'create',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is a required entry.'
			)
		),
		'taxtype' => array(
			'Required' => array(
				'rule' => 'notEmpty',
				'on'=>'create',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is a required entry.'
			)
		),
		'taxrate' => array(
			'Required' => array(
				'rule' => array('range', 0, 1),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This entry must be a non-empty number between 0 and 1.'
			)
		)
	);
	function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val['Customertaxsetup']['calltype'])) {
				$case = $val['Customertaxsetup']['calltype'];
				
				$results[$key]['Customertaxsetup']['calltype'] =$this->calltype($case);
			}
		}
	
		return $results;
	}
}
