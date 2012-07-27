<?php
class Customerbillingaddressmaster extends AppModel {
	var $name = 'Customerbillingaddressmaster';
	var $useTable = 'customerbillingaddressmaster';
	var $primaryKey = 'customerid';
	
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	); 
}
