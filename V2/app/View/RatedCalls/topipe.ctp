<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="cdr_pipe.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Callrecordmaster']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['Callrecordmaster'] as $key => $val){
				$keys[] = $key;
			}
			echo implode('|',$keys);
			echo "\r\n";
		}
		echo implode('|',$row['Callrecordmaster']);
		echo "\r\n";
		$i++;
	endforeach;
	
?>