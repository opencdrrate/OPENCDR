<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="Billingbatches.csv"');
?>

<?php
	$i = 0;
	foreach ($data as $row):
		unset($row['Billingbatchmaster']['rowid']);
		echo implode(',',$row['Billingbatchmaster']);
		echo "\r\n";
	endforeach;
	
?>