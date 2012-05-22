<?php
	include_once 'config.php';

     include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
     $db = pg_connect($connectstring);
     if (!$db) {
     die("Error in connection: " . pg_last_error());
     }

     $rowid = $_GET['rowid'];

     $sql = "DELETE from customertaxsetup where rowid = '$rowid';";
     $result = pg_query($sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     pg_free_result($result);
    
     pg_close($db);

     $host  = $_SERVER['HTTP_HOST'];
     $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
     $extra = 'listcustomertaxsetup.php';
     header("Location: http://$host$uri/$extra");
?>
