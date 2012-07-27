<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="IPAddresses.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Ipaddressmaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Ipaddressmaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Ipaddressmaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>