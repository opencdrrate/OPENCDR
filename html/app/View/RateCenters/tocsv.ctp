<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="rate_centers.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Tieredoriginationratecentermaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Tieredoriginationratecentermaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Tieredoriginationratecentermaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>