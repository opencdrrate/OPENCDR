<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="customer_tax_setups.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Customertaxsetup']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Customertaxsetup'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Customertaxsetup']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>