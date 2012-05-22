<html>
<head>
<script type="text/javascript">
function confirmation() {
	var answer = confirm("Are you sure?")
	if (answer){
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

     $sql = 'DELETE from "CustomerMaster" where rowid = '.$rowid;
     $result = pg_query($sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<font color='red'>Data successfully deleted!</font><br/><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
?>
	}
	else{
		alert("Thanks for sticking around!")
	}
}
</script>
</head>
<body onload="confirmation()">
<p>Suck Dick</p>
</body>
</html>

