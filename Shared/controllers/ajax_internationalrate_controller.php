<?php
$path = $_SERVER["DOCUMENT_ROOT"].'/Shared/';
include_once $path . 'lib/FileUtils/InternationalRateFileImporter.php';
include_once $path . 'DAL/table_internationalratemaster.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$customerid = $_GET['customerid'];

	$table = new psql_internationalratemaster($connectstring);
	$table->Connect();
	$myFile = 'php://input';
	header('Content-Type: text/html');
	header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	
	if(empty($myFile)){
		trigger_error("Please choose a file");
	}
	else{
		$FileImporter = new InternationalRateFileImporter($table, $customerid);
		$FileImporter->Open($myFile);
		while($line = $FileImporter->ImportLine()){
			echo $line;
		}
		$FileImporter->Close();
	}
?>