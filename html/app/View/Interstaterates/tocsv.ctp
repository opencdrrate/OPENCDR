<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="interstate_rates.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Interstateratemaster']['customerid']);
		unset($row['Interstateratemaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Interstateratemaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Interstateratemaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>