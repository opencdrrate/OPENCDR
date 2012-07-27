<?php
class Onetimechargequeue extends AppModel {
	var $name = 'Onetimechargequeue';
	var $useTable = 'onetimechargequeue';
	var $primaryKey = 'onetimechargeid';
	
	var $validate = array(
		'unitamount' => array(
			'unitamount-numeric' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be a non-empty number.'
			)
		),
		'quantity' => array(
			'quantity-numeric' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be a non-empty number.'
			)
		),
		'chargedesc' => array(
			'chargedesc-required' => array(
				'rule' => array('maxLength', 100),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is a required field with a maximum character length of 100.'
			)
		)
	);
}
