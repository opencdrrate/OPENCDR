
<?php
include 'config.php';
if(isset($_GET['done'])){
	$donePage = <<<HEREDOC
	Finished rating<br><br>
	<a href="main.php">Back to main</a>
HEREDOC;
	echo $donePage;
}
else{
	$wait = <<< HEREDOC
	Please wait <br>
	<blink>Working</blink><br>
HEREDOC;
	echo $wait;
	
	$db = pg_connect($connectstring) 
		or die('Database not found or is disconnected
				<br><br><a href="main.php">back</a>');
	set_time_limit ( 0 );
	$categorizeCDR = 'select "fnCategorizeCDR"()';
	$fnRateIndeterminateJurisdictionCDR = 'select "fnRateIndeterminateJurisdictionCDR"()';
	$fnRateInternationalCDR = 'select "fnRateInternationalCDR"()';
	$fnRateInterstateCDR = 'select "fnRateInterstateCDR"()';
	$fnRateIntrastateCDR = 'select "fnRateIntrastateCDR"()';
	$fnRateSimpleTerminationCDR = 'select "fnRateSimpleTerminationCDR"()';
	$fnRateTieredOriginationCDR = 'select "fnRateTieredOriginationCDR"()';
	$fnRateTollFreeOriginationCDR = 'select "fnRateTollFreeOriginationCDR"()';

	$categorizeCDRResult = pg_query($db, $categorizeCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($categorizeCDRResult);
	$categorizeCDRResultReturnCode = $result[0];

	$fnRateIndeterminateJurisdictionCDRResult = pg_query($db, $fnRateIndeterminateJurisdictionCDR) 
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateIndeterminateJurisdictionCDRResult);
	$fnRateIndeterminateJurisdictionCDRReturnCode = $result[0];

	$fnRateInternationalCDRResult = pg_query($db, $fnRateInternationalCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateInternationalCDRResult);
	$fnRateInternationalCDRReturnCode = $result[0];

	$fnRateInterstateCDRResult = pg_query($db, $fnRateInterstateCDR) 
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateInterstateCDRResult);
	$fnRateInterstateCDRReturnCode = $result[0];

	$fnRateIntrastateCDRResult = pg_query($db, $fnRateIntrastateCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateIntrastateCDRResult);
	$fnRateIntrastateCDRReturnCode = $result[0];

	$fnRateSimpleTerminationCDRResult = pg_query($db, $fnRateSimpleTerminationCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateSimpleTerminationCDRResult);
	$fnRateSimpleTerminationCDRReturnCode = $result[0];

	$fnRateTieredOriginationCDRResult = pg_query($db, $fnRateTieredOriginationCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateTieredOriginationCDRResult);
	$fnRateTieredOriginationCDRReturnCode = $result[0];

	$fnRateTollFreeOriginationCDRResult = pg_query($db, $fnRateTollFreeOriginationCDR)  
				or die('<br><br><a href="main.php">back</a>');
	$result = pg_fetch_row($fnRateTollFreeOriginationCDRResult);
	$fnRateTollFreeOriginationCDRReturnCode = $result[0];

	pg_close($db);
	header('location: ratecalls.php?done=y');
	}
?>