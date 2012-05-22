<?php
$path = '../';
include_once $path . 'lib/FileUtils/TollfreeoriginationRateFileImporter.php';
include_once $path . 'DAL/table_tollfreeoriginationratemaster.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

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