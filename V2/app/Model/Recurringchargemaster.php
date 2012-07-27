<?php
class Recurringchargemaster extends AppModel {
	var $name = 'Recurringchargemaster';
	var $useTable = 'recurringchargemaster';
	var $primaryKey = 'recurringchargeid';
	var $validate = array(
		'unitamount' => array(
			'unitamount-nonempty' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty number.'
				)
		),
		'quantity' => array(
			'quantity-nonempty' => array(
					'rule' => 'numeric',
					'required' => true,
					'allowEmpty' => false,
					'message' => 'This field must be a non-empty number.'
				)
		)
	);
}
