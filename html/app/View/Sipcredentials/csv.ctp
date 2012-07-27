<?php
header('Content-Type: csv'); 
?>
CustomerID,SIP Username,SIP Password,SIP Status
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Vwsipcredential']['rowid']);
		/*
		if($i == 0){
			$keys = array();
			foreach ($row['Vwsipcredential'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}*/
		echo implode(',',$row['Vwsipcredential']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>