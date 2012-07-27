<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="cdr.csv"');
?>
<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['CallrecordmasterTbr']['rowid']);
		if($i == 0){
			$keys = array();
			foreach ($row['CallrecordmasterTbr'] as $key => $val){
				$keys[] = $key;
			}
			echo implode(',',$keys);
			echo "\r\n";
		}
		echo implode(',',$row['CallrecordmasterTbr']);
		echo "\r\n";
		$i++;
	endforeach;
?>