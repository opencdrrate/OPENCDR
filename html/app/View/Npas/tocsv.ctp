<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="NPAs.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Npamaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Npamaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Npamaster']);
		echo "\r\n";
		$i++;
	endforeach;
?>