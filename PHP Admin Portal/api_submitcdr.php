<?php
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/TBRLibs.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
$keys = array();

$keys[] = 'callid';
$keys[] = 'customerid';
$keys[] = 'calltype';
$keys[] = 'calldatetime';
$keys[] = 'duration';

$keys[] = 'direction';
$keys[] = 'sourceip';
$keys[] = 'originatingnumber';
$keys[] = 'destinationnumber';
$keys[] = 'lrn';

$keys[] = 'cnamdipped';
$keys[] = 'ratecenter';
$keys[] = 'carrierid';
$keys[] = 'wholesalerate';
$keys[] = 'wholesaleprice';

$item = array();
$item['cnamdipped'] = 'f';
foreach($keys as $key){
	if(isset($_POST[$key])){
		$item[$key] = $_POST[$key];
	}
}
/*
$item['callid'] = $row['AnswerTime'] 
							. '_' . $row['CallingNumber'] 
							. '_' . $row['CalledNumber']
							. '_' . $row['TotalSeconds'];
*/
if(!isset($_POST['callid'])){
	if(isset($_POST['calldatetime']) and isset($_POST['originatingnumber'])
	and isset($_POST['destinationnumber']) and isset($_POST['duration'])){
		$item['callid'] = $_POST['calldatetime'] . '_' . $_POST['originatingnumber'] 
			. '_' . $_POST['destinationnumber'] . '_' . $_POST['duration'];
	}
}

if(isset($item['duration']) and $item['duration'] != "0" and isset($item['callid']) and isset($_POST['originatingnumber'])
	and isset($_POST['destinationnumber']) and isset($_POST['duration']) and isset($_POST['calldatetime'])){
	echo 'Item sent<br>';
	print_r($item);
	$allItems = array();
	$allItems[] = $item;
	InsertDataIntoCallRecordMaster($allItems, $connectstring);
}
else{
	echo 'failed<br>';
	print_r($item);
}
?>