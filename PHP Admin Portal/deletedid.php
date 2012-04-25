<?php

     include 'config.php'; 
	 include 'DAL/table_didmaster.php';
     $rowid = $_GET['rowid'];
	 
	 $table = new psql_didmaster($connectstring);
	 
	 $table->Connect();
	 $table->Delete(array('rowid' => $rowid));
	 $table->Disconnect();
	 
     $host  = $_SERVER['HTTP_HOST'];
     $uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
     $extra = 'listdids.php';
     header("Location: http://$host$uri/$extra");
    
?>
