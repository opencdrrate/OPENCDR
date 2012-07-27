<?php

class AsteriskServer extends AppModel{
	var $name = 'AsteriskServer';
	var $useTable = 'asteriskserversetup';
	var $primaryKey = 'rowid';
	
	var $validate = array(
		'servername' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'serveripordns' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'mysqlport' => array(
			'servername-nonempty' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'mysqllogin' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'mysqlpassword' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'cdrdatabase' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		),
		'cdrtable' => array(
			'servername-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This field must be non-empty and not already exist.'
			)
		)
	);
}

?>