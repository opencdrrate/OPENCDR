<?php
include_once 'AbstractFileImporter.php';
class VitelityFileImporter extends AbstractFileImporter{
	function VitelityFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
	/*Date,Source,Destination,Seconds,CallerID,Disposition,Cost*/
		list($Date, $Source,$Destination,$Seconds,$CallerID, 
				$Disposition,$Cost) = $data;
			if($Seconds == 0 or $Seconds == '0'){
				return false;
			}
			$row = array();
			
			  $row['calldatetime'] = $Date;
			  $row['source'] = $Source;
			  $row['destination'] = $Destination;
			  $row['seconds'] = $Seconds;
			  $row['callerid'] = $CallerID;
			  $row['disposition'] = $Disposition;
			  $row['cost'] = $Cost;
			  
		$oldParams = array('source' => $row['source'],
							'destination' => $row['destination'],
							'calldatetime' => $row['calldatetime'],
							'seconds' => $row['seconds']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$row);
	}
}
?>