<?php
/*
connectString - A valid SQL connection string
page - The page you want to redirect to.
sqlStatement - the sql string you want to execute
*/
$connectString = $_POST["connectString"];
$page = $_POST["page"];
$sqlStatement = $_POST["sqlStatement"];

$db = pg_connect($connectString) or die();
#execute statement
pg_query($db, $sqlStatement) or die();
#Redirect to main
pg_close($db) or die();
header('location: ' . $page);
?>