<?php
#$path = $_SERVER["DOCUMENT_ROOT"].'/PHP Admin Portal';
$path = '..';
include_once $path . '/lib/FileUtils/InterstateRateFileImporter.php';
include_once $path . '/DAL/table_interstateratemaster.php';
include_once $path . '/config.php';
include_once $path . '/controllers/XMLResponse/XMLResponse.php';
$customerid = $_GET['customerid'];
	
	$table = new psql_interstateratemaster($connectstring);
	$table->Connect();
	$myFile = 'php://input';
	header('Content-Type: text/html');
	header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	if(empty($myFile)){
		trigger_error("Please choose a file");
	}
	else{
		$FileImporter = new InterStateRateFileImporter($table, $customerid);
		$FileImporter->Open($myFile);
		while($line = $FileImporter->ImportLine()){
			echo $line;
		}
		$FileImporter->Close();
	}
	//echo $FileImporter->GetSummary();
?>