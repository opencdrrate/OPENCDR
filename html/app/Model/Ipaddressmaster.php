<?php
class Ipaddressmaster extends AppModel {
	var $name = 'Ipaddressmaster';
	var $useTable = 'ipaddressmaster';
	var $primaryKey = 'rowid';
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	var $validate = array(
		'ipaddress'=>array(
			'ipaddress-required' => array(
				'rule' => 'ip', // or 'IPv6' or 'both' (default)
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Please supply a valid IP address.'
			)
		)
	);
}
