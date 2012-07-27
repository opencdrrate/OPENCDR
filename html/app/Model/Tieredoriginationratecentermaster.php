<?php
class Tieredoriginationratecentermaster extends AppModel {
	var $name = 'Tieredoriginationratecentermaster';
	var $useTable = 'tieredoriginationratecentermaster';
	var $primaryKey = 'rowid';
	
	var $validate = array(
		'ratecenter' => array(
				'rule' => 'isUnique',
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create',
				'message' => 'This is a required field and must be unique.'
		),
		'tier' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field is required and must be a number.'
		)
	);
}
