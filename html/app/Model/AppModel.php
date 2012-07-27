<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

	function rateCalls(){
		
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$functions = array('fnCategorizeCDR',
							'fnRateIndeterminateJurisdictionCDR',
							'fnRateInternationalCDR',
							'fnRateInterstateCDR',
							'fnRateIntrastateCDR',
							'fnRateSimpleTerminationCDR',
							'fnRateTieredOriginationCDR',
							'fnRateTollFreeTerminationCDR',
							'fnRateTollFreeOriginationCDR');
		foreach($functions as $function){
			$exeString = <<< HEREDOC
			SELECT "{$function}"();
HEREDOC;
			$db->rawQuery($exeString);
		}
	}
	
	function categorizecdrs(){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$exeString = <<< HEREDOC
			SELECT "fnCategorizeCDR"();
HEREDOC;
		$db->rawQuery($exeString);
	}
	
	function RunStoredProcedure($sp){
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$exeString = <<< HEREDOC
			SELECT "{$sp}"();
HEREDOC;
		return $db->rawQuery($exeString);
	}
	/** 
         * checks is the field value is unqiue in the table 
         * note: we are overriding the default cakephp isUnique test as the 
original appears to be broken 
         * 
         * @param string $data Unused ($this->data is used instead) 
         * @param mnixed $fields field name (or array of field names) to 
validate 
         * @return boolean true if combination of fields is unique 
         */ 

	function checkUnique($data, $fields) { 
        if (!is_array($fields)) { 
            $fields = array($fields); 
        } 
        foreach($fields as $key) { 
            $tmp[$key] = $this->data[$this->name][$key]; 
        } 
        if (isset($this->data[$this->name][$this->primaryKey])) { 
            $tmp[$this->primaryKey] = "<>".$this->data[$this->name][$this->primaryKey]; 
		}
        return $this->isUnique($tmp, false); 
    }
	
	function notExists($check){
		$count = $this->find( 'count', array('conditions' => $check, 'recursive' => -1) );
		return $count < 1;
	}
	
	function anyDate($data){
		$date = array_shift($data);
		$result = strtotime($date) !== false;
		return $result;
	}
	
	function customexecute($query){
		
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$results = $db->execute($query);
			return $db->lastAffected();
	}
	
	function calltype($numeric){
				$case = $numeric;
				$new = $case;
				if($case == '5'){
					$new = 'Intrastate';
				}
				else if($case == '10'){
					$new = 'Interstate';
				}
				else if($case == '15'){
					$new = 'Tiered Origination';
				}
				else if($case == '20'){
					$new = 'Termination of Indeterminate Jurisdiction';
				}
				else if($case == '25'){
					$new = 'International';
				}
				else if($case == '30'){
					$new = 'Toll-free Origination';
				}
				else if($case == '35'){
					$new = 'Simple Termination';
				}
				else if($case == '40'){
					$new = 'Toll-free Termination';
				}
				else if($case == '60'){
					$new = 'Retail Termination';
				}
				else if($case == '65'){
					$new = 'Retail Origination';
				}
				else if($case == '68'){
					$new = 'Retail Origination (Toll-free)';
				}
				else{
					$new = 'Unknown';
				}
				return $new;
	}
}
