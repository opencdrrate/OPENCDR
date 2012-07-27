<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="uncategorized_cdr.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['UncategorizedCdr']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['UncategorizedCdr'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['UncategorizedCdr']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>