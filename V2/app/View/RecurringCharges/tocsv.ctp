<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="recurring_charges.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Recurringchargemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Recurringchargemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Recurringchargemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>