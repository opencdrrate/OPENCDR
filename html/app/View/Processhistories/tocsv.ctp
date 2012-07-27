<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="process_history.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Processhistory']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Processhistory'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['Processhistory']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>