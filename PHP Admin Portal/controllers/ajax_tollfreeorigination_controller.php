<?php
#$path = $_SERVER["DOCUMENT_ROOT"].'/PHP Admin Portal';
$path = '..';
include_once $path . '/lib/FileUtils/TollfreeoriginationRateFileImporter.php';
include_once $path . '/DAL/table_tollfreeoriginationratemaster.php';
include_once $path . '/config.php';
$customerid = $_GET['customerid'];

	$table = new psql_tollfreeoriginationratemaster($connectstring);
	$table->Connect();
	header('Content-Type: text/html');
	header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	$myFile = 'php://input';
	if(empty($myFile)){
		trigger_error("Please choose a file");
	}
	else{
		$FileImporter = new TollfreeoriginationRateFileImporter($table, $customerid);
		$FileImporter->Open($myFile);
		while($line = $FileImporter->ImportLine()){
			echo $line;
		}
		$FileImporter->Close();
	}
?>