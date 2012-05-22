<html>
<head>
<title>ADHOC Query Export</title>
 
<script language="javascript"> 
function MyFormSubmit(){ 
document.export.submit(); 
} 
</script> 

 
<?php 
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();


	$db = pg_connect($connectstring);

	if ($_POST["queryString"] == "") {
		
		echo "Must enter a valid query";
		echo "<br />";
		echo "<a href='adhocquery.php'><--Back to query page</a>";
		exit();

	} else {

	
		$query = $_POST["queryString"];

		$csv_output = "";
		$csv_hdr = "";

		$result = pg_query($query);
		if (!$result) {
			echo "Problem with query " . $query . "<br/>";
			echo pg_last_error();
			exit();
		}

		$count = 1;
		while($myrow = pg_fetch_assoc($result)) {
		if($count == 1){
			$first = 1;
			foreach ($myrow as $key => $value) {
				if($first != 1){
					$csv_hdr .= "|";
				}
			
    	    			$csv_hdr .= "$key";
				$first = $first + 1;
			}
		}
			$first = 1;
			foreach ($myrow as $key => $value) {

				if($first != 1){
					$csv_output .= "|";
				}

    	    			$csv_output .= "$value";
				$first = $first + 1;
			}

            	$csv_output .= "\n";
	    	$count = $count + 1;

        	}
	}

?>  
	</head>
	<body onload="MyFormSubmit()"> 
	<form name="export" action="exportpipe.php" method="post">
    	<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
    	<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
	<input type="hidden" value="AdHocExport" name="filename">
	</form>
	</body>
	</html>