
<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
	
	$categorizeDoneMsg = 'Categorization done.  Result: ';
	$fnRateIndeterminateJurisdictionCDRDoneMsg = 'RateIndeterminate done.  Result:';
	$fnRateInternationalCDRDoneMsg = 'RateInternational done.  Result:';
	$fnRateInterstateCDRDoneMsg = 'RateInterstate done.  Result:';
	$fnRateIntrastateCDRDoneMsg = 'RateIntrastate done.  Result:';
	$fnRateSimpleTerminationCDRDoneMsg = 'RateSimpleTermination done.  Result:';
	$fnRateTieredOriginationCDRDoneMsg = 'RateTieredOrigination done.  Result:';
	$fnRateTollFreeOriginationCDRDoneMsg = 'RateTollFreeOrigination done.  Result:';
	
	$estimatedHeaderSize = 0;
	$estimatedHeaderSize += strlen($categorizeDoneMsg);
	$estimatedHeaderSize += strlen($fnRateIndeterminateJurisdictionCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateInternationalCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateInterstateCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateIntrastateCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateSimpleTerminationCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateTieredOriginationCDRDoneMsg);
	$estimatedHeaderSize += strlen($fnRateTollFreeOriginationCDRDoneMsg);
	
	header('Content-Type: text/html');
	header('Content-Length: ' . $estimatedHeaderSize+250);
	echo $estimatedHeaderSize . '<br>';
	$handle = fopen('php://input', 'rb');
	$db = pg_connect($connectstring) 
		or die('Error : Database not found or is disconnected');
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
				or die('Error : fnCategorizeCDR');
	$result = pg_fetch_row($categorizeCDRResult);
	$categorizeCDRResultReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $categorizeDoneMsg 
		. ' ' . $categorizeCDRResultReturnCode . '<br>';
	
	$fnRateIndeterminateJurisdictionCDRResult = pg_query($db, $fnRateIndeterminateJurisdictionCDR) 
				or die('Error : fnRateIndeterminateJurisdictionCDR');
	$result = pg_fetch_row($fnRateIndeterminateJurisdictionCDRResult);
	$fnRateIndeterminateJurisdictionCDRReturnCode = $result[0];
	sleep(1);
	echo fread($handle, 1);
	echo $fnRateIndeterminateJurisdictionCDRDoneMsg 
		. ' ' . $fnRateIndeterminateJurisdictionCDRReturnCode . '<br>';
	
	$fnRateInternationalCDRResult = pg_query($db, $fnRateInternationalCDR)  
				or die('Error : fnRateInternationalCDR');
	$result = pg_fetch_row($fnRateInternationalCDRResult);
	$fnRateInternationalCDRReturnCode = $result[0];
	sleep(1);
	echo fread($handle, 1);
	echo $fnRateInternationalCDRDoneMsg 
		. ' ' . $fnRateInternationalCDRReturnCode . '<br>';
	
	$fnRateInterstateCDRResult = pg_query($db, $fnRateInterstateCDR) 
				or die('Error : fnRateInterstateCDR');
	$result = pg_fetch_row($fnRateInterstateCDRResult);
	$fnRateInterstateCDRReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $fnRateInterstateCDRDoneMsg 
		. ' ' . $fnRateInterstateCDRReturnCode . '<br>';
	
	$fnRateIntrastateCDRResult = pg_query($db, $fnRateIntrastateCDR)  
				or die('Error : fnRateIntrastateCDR');
	$result = pg_fetch_row($fnRateIntrastateCDRResult);
	$fnRateIntrastateCDRReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $fnRateIntrastateCDRDoneMsg 
		. ' ' . $fnRateIntrastateCDRReturnCode . '<br>';
	
	$fnRateSimpleTerminationCDRResult = pg_query($db, $fnRateSimpleTerminationCDR)  
				or die('Error : fnRateSimpleTerminationCDR');
	$result = pg_fetch_row($fnRateSimpleTerminationCDRResult);
	$fnRateSimpleTerminationCDRReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $fnRateSimpleTerminationCDRDoneMsg 
		. ' ' . $fnRateSimpleTerminationCDRReturnCode . '<br>';
	
	$fnRateTieredOriginationCDRResult = pg_query($db, $fnRateTieredOriginationCDR)  
				or die('Error : fnRateTieredOriginationCDR');
	$result = pg_fetch_row($fnRateTieredOriginationCDRResult);
	$fnRateTieredOriginationCDRReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $fnRateTieredOriginationCDRDoneMsg 
		. ' ' . $fnRateTieredOriginationCDRReturnCode . '<br>';
	
	$fnRateTollFreeOriginationCDRResult = pg_query($db, $fnRateTollFreeOriginationCDR)  
				or die('Error : fnRateTollFreeOriginationCDR');
	$result = pg_fetch_row($fnRateTollFreeOriginationCDRResult);
	$fnRateTollFreeOriginationCDRReturnCode = $result[0];
	sleep(1); 
	echo fread($handle, 1);
	echo $fnRateTollFreeOriginationCDRDoneMsg 
		. ' ' . $fnRateTollFreeOriginationCDRReturnCode . '<br>';
	fclose($handle);
	pg_close($db);
	
?>