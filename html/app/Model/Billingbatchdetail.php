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
	
	public function GetTaxDetails($billingbatchid, $customerid){
		$taxdetails = [];
		$items = $this->find('all',
				array(
					'fields' => array('SUM(lineitemamount) as "lineitemamount"', 'lineitemdesc'),
					'group' => array('Billingbatchdetail.lineitemdesc'),
					'conditions' => array(
						'Billingbatchdetail.billingbatchid' => $billingbatchid,
						'Billingbatchdetail.customerid' => $customerid,
						'Billingbatchdetail.lineitemtype' => 40
					)
				)
			);
		foreach($items as $item){
			$detail = [];
			$desc = $item['Billingbatchdetail']["lineitemdesc"];
			$amount = $item[0]["lineitemamount"];
			$detail['Billingbatchdetail'] = array('lineitemdesc' => $desc, 'lineitemamount' => $amount);
			$taxdetails[] = $detail;
		}
		
		return $taxdetails;
	}
}
