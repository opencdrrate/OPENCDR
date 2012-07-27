<?php
class Vitelitycdr extends AppModel{
	var $name = 'Vitelitycdr';
	var $primaryKey = 'rowid';
	var $useTable = 'vitelitycdr';
	var $actsAs = array('ImportCsv');
	
	function loadtype($line,$type){
	/*Date,Source,Destination,Seconds,CallerID,Disposition,Cost*/
		list($Date, $Source,$Destination,$Seconds,$CallerID, 
				$Disposition,$Cost) = str_getcsv($line,',');
			$row = array();
			
			  $row['calldatetime'] = $Date;
			  $row['source'] = $Source;
			  $row['destination'] = $Destination;
			  $row['seconds'] = $Seconds;
			  $row['callerid'] = $CallerID;
			  $row['disposition'] = $Disposition;
			  $row['cost'] = $Cost;
			  
			if(is_numeric($Seconds)){
				if($Seconds == 0){
					return false;
				}
				return $row;
			}
			else{
				return false;
			}
	}
	
	function MoveToCdr(){
		$numberofdetails = $this->find('count');
		$moveString = 'SELECT "fnMoveVitelityCDRToTBR"();';
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$db->rawQuery($moveString);
		return $numberofdetails . ' items inserted.';
	}
}
?>