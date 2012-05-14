<?php
include_once '../DAL/Functions/DBFunction.php';
include_once '../config.php';
if(isset($_SERVER['HTTP_X_SPNAME'])){
	$spName = $_SERVER['HTTP_X_SPNAME'];
	$dbfunction = new DBFunction($spName, $connectstring);
	$result = $dbfunction->Run();
}
?>