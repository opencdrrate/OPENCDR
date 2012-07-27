<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="intrastate_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Intrastateratemaster']['customerid']);
		unset($row['Intrastateratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Intrastateratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Intrastateratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>