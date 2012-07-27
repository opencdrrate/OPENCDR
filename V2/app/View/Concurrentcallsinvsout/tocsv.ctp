<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="Concurrentcallsinvsout.csv"');
?>

<?php
	$i = 0;
	foreach ($data as $row):
		echo implode(',',$row['Concurrentcallsinvsout']);
		echo ',';
		echo implode(',',$row[0]);
		echo "\r\n";
	endforeach;
	
?>