<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="onetime_charges.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Onetimechargequeue']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Onetimechargequeue'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Onetimechargequeue']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>