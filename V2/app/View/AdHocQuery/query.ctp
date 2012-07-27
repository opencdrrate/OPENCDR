<?php
header('Content-Type: text/plain'); 
?>
<?php
	if(isset($rowsAffected)){
		echo $rowsAffected . " rows affected.";
	}
	$i = 0;
	foreach ($results as $item):
		echo implode(',',$item[0]);
		echo "\r\n";
	endforeach;
?>