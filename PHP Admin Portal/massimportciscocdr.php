<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'vars/ciscoconfig.php';
include_once $path . 'lib/TBRLibs.php';
include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

$fileArray = scandir($CDRSourcePath);
foreach($fileArray as $fileName){
	$fullFilePath = $CDRSourcePath . '/' .$fileName;
	$destinationFilePath = $CDRProcessedPath . '/' . $fileName;
	$fileType = filetype($fullFilePath);
	if($fileType == 'dir'){
		continue;
	}
	echo $fileName . ' : <br>';
	
	$fh = fopen($fullFilePath, 'r');
	$theData = fread($fh, filesize($fullFilePath));
	fclose($fh);
	echo ProcessCisco($theData, $connectstring);
	if(rename($fullFilePath, $destinationFilePath)){ #move a file
		echo $fileName . ' moved to ' . $CDRProcessedPath . '<br>';
	}
	else{
		echo 'Failed to move : '.$filename . '<br>';
	}
}
?>