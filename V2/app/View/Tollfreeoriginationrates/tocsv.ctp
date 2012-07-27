<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="tollfree_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Tollfreeoriginationratemaster']['customerid']);
		unset($row['Tollfreeoriginationratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Tollfreeoriginationratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Tollfreeoriginationratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>