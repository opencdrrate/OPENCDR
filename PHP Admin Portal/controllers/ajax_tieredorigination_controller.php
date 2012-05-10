<?php
#$path = $_SERVER["DOCUMENT_ROOT"].'/PHP Admin Portal';
$path = '..';
include_once $path . '/lib/FileUtils/TieredoriginationRateFileImporter.php';
include_once $path . '/DAL/table_tieredoriginationratemaster.php';
include_once $path . '/config.php';
$customerid = $_GET['customerid'];
	header('Content-Type: text/html');
	header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	$table = new psql_tieredoriginationratemaster($connectstring);
	$table->Connect();
	$myFile = 'php://input';
	if(empty($myFile)){
		trigger_error("Please choose a file");
	}
	else{
		$FileImporter = new TieredoriginationRateFileImporter($table, $customerid);
		$FileImporter->Open($myFile);
		while($line = $FileImporter->ImportLine()){
			echo $line;
		}
		$FileImporter->Close();
	}
?>