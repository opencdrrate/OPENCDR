<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="customers.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Customer']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Customer'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Customer']);
		echo "\r\n";
		$i++;
	endforeach;
?>