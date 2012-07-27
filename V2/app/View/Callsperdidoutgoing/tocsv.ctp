<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="callsperdidoutgoing.csv"');
?>

<?php
	$i = 0;
	foreach ($data as $row):
		echo implode(',',$row['Callrecordmaster']);
		echo "\r\n";
	endforeach;
	
?>