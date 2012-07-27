<?php
class Customerretailplanmaster extends AppModel {
	var $name = 'Customerretailplanmaster';
	var $useTable = 'customerretailplanmaster';
	var $primaryKey = 'rowid';
	var $belongsTo = array('Customer' => 
						array('className' => 'Customer',
							'foreignKey' => 'customerid')/*,
						'retailplan' => array('className'=>'Retailplanmaster',
											'foreignKey'=>'planid')*/
					);
	var $validate = array(
		'planid' => array(
			'planid-nonempty' => array(
					'rule' => array('minLength', 0),
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field is required.'
			)
		),
		'customerid' => array(
			'customerid-nonempty' => array(
					'rule' => array('minLength', 0),
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field is required.'
			)
		)
	);
}
