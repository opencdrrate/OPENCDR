<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="simpletermination_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Simpleterminationratemaster']['customerid']);
		unset($row['Simpleterminationratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Simpleterminationratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Simpleterminationratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>