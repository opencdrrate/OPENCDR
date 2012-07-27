<?php
class Paymentmaster extends AppModel {
	var $name = 'Paymentmaster';
	var $useTable = 'paymentmaster';
	var $primaryKey = 'rowid';
	
	var $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	
	var $validate = array(
		'paymentamount' => array(
			'paymentamount-isNumeric-nonempty' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This entry must be a non-empty number.'
			)
		)
	);
}
