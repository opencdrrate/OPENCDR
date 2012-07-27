<?php
class Billingbatchmaster extends AppModel {
	var $name = 'Billingbatchmaster';
	var $useTable = 'billingbatchmaster';
	var $primaryKey = 'billingbatchid';
	
	var $hasMany = array(
		'Detail' => array(
			'className' => 'Billingbatchdetail',
			'foreignKey' => 'billingbatchid'
		)
	);
	
	var $validate = array(
		'billingbatchid' => array(
			'unique' => array(
				'rule' => array('minLength',0),
				'required' => true,
				'allowEmpty' => false,
				'on' => 'create',
				'message' => 'This field must be non-empty and not already exist.'
			),
			'fifteenandunder' => array(
				'rule' => array('maxLength',15),
				'message' => 'This field must be under 16 characters long.'
			)
		)
	);
	
	
	function GenerateBillingBatch($data){
		foreach($data as $batchdata){
			$batchid = $batchdata['billingbatchid'];
			$billingdate = <<< HEREDOC
				{$batchdata['billingdate']['year']}-{$batchdata['billingdate']['month']}-{$batchdata['billingdate']['day']}
HEREDOC;
			$billingduedate = <<< HEREDOC
				{$batchdata['duedate']['year']}-{$batchdata['duedate']['month']}-{$batchdata['duedate']['day']}
HEREDOC;
			$billingcycleid = $batchdata['billingcycleid'];
			$usageperiodend = <<< HEREDOC
				{$batchdata['usageperiodend']['year']}-{$batchdata['usageperiodend']['month']}-{$batchdata['usageperiodend']['day']}
HEREDOC;
			$recurringfeestart = <<< HEREDOC
				{$batchdata['recurringfeestart']['year']}-{$batchdata['recurringfeestart']['month']}-{$batchdata['recurringfeestart']['day']}
HEREDOC;
			$recurringfeeend = <<< HEREDOC
				{$batchdata['recurringfeeend']['year']}-{$batchdata['recurringfeeend']['month']}-{$batchdata['recurringfeeend']['day']}
HEREDOC;
			
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sqlStatement = "select \"fnGenerateBillingBatch\"('$batchid', '$billingdate', '$billingduedate', '$billingcycleid',  '$usageperiodend', '$recurringfeestart', '$recurringfeeend');"; 
			
			$db->rawQuery($sqlStatement);
		}
		return true;
	}
	
	function DeleteBillingBatch($batchid){
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sqlStatement = "select \"fnDeleteBillingBatch\"('$batchid');"; 
			
			$db->rawQuery($sqlStatement);
			return true;
	}
}
