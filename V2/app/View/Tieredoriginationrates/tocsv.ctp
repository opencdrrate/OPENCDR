<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="tieredorigination_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Tieredoriginationratemaster']['customerid']);
		unset($row['Tieredoriginationratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Tieredoriginationratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Tieredoriginationratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>