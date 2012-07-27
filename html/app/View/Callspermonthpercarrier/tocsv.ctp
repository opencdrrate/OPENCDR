<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="callspermonthpercarrier.csv"');
?>

<?php
	$i = 0;
	foreach ($data as $row):
		echo implode(',',$row['Callspermonthpercarrier']);
		echo "\r\n";
	endforeach;
	
?>