<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="Dids.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Didmaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Didmaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Didmaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>