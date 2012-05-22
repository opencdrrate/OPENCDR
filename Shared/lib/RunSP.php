<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'DAL/Functions/DBFunction.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

if(isset($_SERVER['HTTP_X_SPNAME'])){
	$spName = $_SERVER['HTTP_X_SPNAME'];
	$dbfunction = new DBFunction($spName, $connectstring);
	$result = $dbfunction->Run();
}
?>