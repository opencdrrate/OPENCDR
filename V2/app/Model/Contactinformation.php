<?php
class Contactinformation extends AppModel{
	var $name = 'Contactinformation';
	var $useTable = 'customercontactmaster';
	var $primaryKey = 'customerid';
	var $validate = array(
		'primaryemailaddress' => array(
			'primaryemailaddress-nonempty' => array(
				'rule' => 'email',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is not a valid email address.'
			)
		)
	);
}
?>