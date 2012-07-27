<?php
class Billingbatchdetail extends AppModel {
	var $name = 'Billingbatchdetail';
	var $primaryKey = 'rowid';
	
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		),
		'BillingBatch' => array(
			'className' => 'Billingbatchmaster',
			'foreignKey' => 'billingbatchid'
		)
	); 
}
