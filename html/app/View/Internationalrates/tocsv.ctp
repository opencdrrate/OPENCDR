<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="international_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Internationalratemaster']['customerid']);
		unset($row['Internationalratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Internationalratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Internationalratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>