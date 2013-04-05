<?php
class Customer extends AppModel {
	var $name = 'Customer';
	var $useTable = 'customermaster';
	var $primaryKey = 'customerid';
	var $virtualFields = array(
		'totalpayments' => 0,
		'totalbilled' => 0
    );

	var $hasOne = array(
		'RetailPlan' => array(
			'className' => 'Customerretailplanmaster',
			'conditions' => array('Customer.customertype' => 'Retail'),
			'foreignKey' => 'customerid'
		),
		'Sipcredential' => array(
			'className' => 'Sipcredential',
			'foreignKey' => 'customerid',
			'dependent' =>true
		),
		'BillingAddress' => array(
			'className' => 'Customerbillingaddressmaster',
			'foreignKey' => 'customerid',
			'dependent' =>true
		),
		'ContactInformation' => array(
			'className' => 'Contactinformation',
			'foreignKey' => 'customerid',
			'dependent' =>true
		),
		'Unbilledcdr' => array(
			'className' => 'Unbilledcdr',
			'foreignKey' => 'customerid'
		),
		'LoginInfo' => array(
			'className' => 'User',
			'foreignKey' => 'customerid'
		)
	); 
	
	var $hasMany = array(
		'Payment' => array(
			'className' => 'Paymentmaster',
			'foreignKey' => 'customerid'
		),
		'BillingDetail' => array(
			'className' => 'Billingbatchdetail',
			'foreignKey' => 'customerid'
		)
	);
	
	var $validate = array(
		'customerid' => array(
			'customerid-nonempty' => array(
				'rule' => array('minLength', 0),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create',
				'message' => 'This field must be non-empty and not already exist.'
			),
			'customerid-alphanumeric' => array(
				'rule' => 'alphanumeric',
				'message' => 'Customer ID can only have numbers and letters with no whitespace.'
			)
		),
		'customername' => array(
			'customername-nonempty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This is a required field.'
			)
		),
		'lrndiprate' => array(
			'lrndiprate-isNumeric-nonempty' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This must be a number between 0 and 1.'
			),
			'lrndiprate-greaterthanequal0' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'This must be a number between 0 and 1.'
			),
			'lrndiprate-lessthan1' => array(
				'rule' => array('comparison', '<', 1),
				'message' => 'This must be a number between 0 and 1.'
			)
		),
		'cnamdiprate' => array(
			'cnamdiprate-isNumeric-nonempty' => array(
				'rule' => 'numeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'This must be a number between 0 and 1.'
			),
			'cnamdiprate-greaterthanequal0' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'This must be a number between 0 and 1.'
			),
			'cnamdiprate-lessthan1' => array(
				'rule' => array('comparison', '<', 1),
				'message' => 'This must be a number between 0 and 1.'
			)
		),
		'creditlimit' => array(
			'creditlimit-isNumeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => true,
				'message' => 'This must be a number.'
			),
			'creditlimit-greaterthanequal0' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'This must be positive.'
			)
		)
	);
	function afterFind($results, $primary = false) {
		// transform calltype
		foreach ($results as $key => $val) {
			if (isset($val['Customer']['indeterminatejurisdictioncalltype'])) {
				$case = $val['Customer']['indeterminatejurisdictioncalltype'];
				
				$results[$key]['Customer']['indeterminatejurisdictioncalltype'] = $this->calltype($case);
			}
		}
		
		//Calculate total payments
		foreach ($results as $key => $val) {
			if(isset($val['Payment'])){
				$total = 0;
				foreach($val['Payment'] as $payment){
					$total += $payment['paymentamount'];
				}
				$results[$key]['Customer']['totalpayments'] = $total;
			}
		}
		//Calculate total bills
		foreach ($results as $key => $val) {
			if(isset($val['BillingDetail'])){
				$total = 0;
				foreach($val['BillingDetail'] as $detail){
					$total += $detail['lineitemamount'];
				}
				$results[$key]['Customer']['totalbilled'] = $total;
			}
		}
		return $results;
	}
	
}
