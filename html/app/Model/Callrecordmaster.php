<?php
class Callrecordmaster extends AppModel {
	var $name = 'Callrecordmaster';
	var $useTable = 'callrecordmaster';
	var $primaryKey = 'rowid';
	
	
	function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($val['Callrecordmaster']['calltype'])) {
				$case = $val['Callrecordmaster']['calltype'];
				
				$results[$key]['Callrecordmaster']['calltype'] = $this->calltype($case);
			}
			if (isset($val['Callrecordmaster']['canbefree'])) {
				$case = $val['Callrecordmaster']['canbefree'];
				$new = $case;
				if($case == '0'){
					$new = 'No';
				}
				else if($case == '1'){
					$new = 'Yes';
				}
				else{
					$new = '';
				}
				$results[$key]['Callrecordmaster']['canbefree'] = $new;
			}
		}
	
		return $results;
	}
}
