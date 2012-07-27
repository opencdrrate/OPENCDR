<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="retailterminationrates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Retailplanterminationrate']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Retailplanterminationrate'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Retailplanterminationrate']);
		echo "\r\n";
		$i++;
	endforeach;
?>