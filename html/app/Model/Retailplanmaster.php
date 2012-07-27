<?php
class Retailplanmaster extends AppModel {
	var $name = 'Retailplanmaster';
	var $useTable = 'retailplanmaster';
	var $primaryKey = 'planid';
	
	var $hasMany = array(
		'TerminationRate' => array(
			'className' => 'Retailplanterminationrate',
			'foreignKey' => 'planid',
			'dependent' =>true
		)
	/*
		'customerretailplan' => array(
			'className' => 'Customerretailplanmaster',
			'foreignKey' => 'planid',
			'counterCache' => true,
			'dependent' =>true
		)*/
	);
	var $validate = array(
		'planid' => array(
			'planid-nonempty' => array(
					'rule' => 'isUnique',
					'required' => true,
					'allowEmpty' => false,
					'on' => 'create',
					'message' => 'This field must be non-empty and unique.'
			),
			'planid-maxlength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'The maximum length is 15 characters long.'
			)
		),
		'originationrate' => array(
			'originationrate-numeric' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty numeric.'
			),
			'originationrate-greaterthanzero' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'This field must be a positive number.'
			)
		),
		'servicefee' => array(
			'servicefee-numeric' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty numeric.'
			),
			'servicefee-greaterthanzero' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'This field must be a positive number.'
			)
		),
		'tollfreeoriginationrate' => array(
			'tollfreeoriginationrate-numeric' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty numeric.'
			),
			'tollfreeoriginationrate-greaterthanzero' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'This field must be a positive number.'
			)
		),
		'freeminutespercycle' => array(
			'freeminutespercycle-numeric' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty numeric.'
			),
			'freeminutespercycle-greaterthanzero' => array(
					'rule' => array('comparison', '>=', 0),
					'message' => 'This field must be a positive number.'
			)
		)
	);
}