

<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/session.php';
include_once $path . 'DAL/table_webportalaccesstokens.php';
include_once $path . 'DAL/table_webportalaccess.php';
include_once $path . 'DAL/table_callrecordmaster.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/XMLTableData.php';

$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

if(!isset($_GET['TOKEN'])){
	#You need to be logged in to view this page
	header('location: login.php?error=notloggedin');
}
$token = $_GET['TOKEN'];

if(IsTokenExpired($token, $connectstring)){
	header('location: login.php?error=notloggedin');
}
else{
	UpdateExpiry($token,$connectstring);
}

$webportalaccess = new psql_webportalaccesstokens($connectstring);
$webportalaccess->Connect();
$webportalaccessInfo = $webportalaccess->Select($token);
$webportalaccess->Disconnect();
$username = $webportalaccessInfo[0]['customerid'];

$user = new psql_webportalaccess($connectstring);
$user->Connect();
$userInfo = $user->Select($username);
$user->Disconnect();
$customerid = $userInfo[0]['customerid'];

//Get function request through post data
$function = $_GET['FUNCTION'];
//Execute function
if($function == "SELECT"){
	header('Content-Type: text/xml');
	//header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	
	$callrecordmasterDetails = new psql_callrecordmaster($connectstring);
	$callrecordmasterDetails->Connect();
	$callrecordmasterDetailsInfo = $callrecordmasterDetails->Select($customerid);
	$callrecordmasterDetails->Disconnect();
	
	//Data to xml
	$xmlData = new XMLTableData();
	$xmlData->AddColumn('callid');
	$xmlData->AddColumn('calltype');
	$xmlData->AddColumn('calldatetime');
	$xmlData->AddColumn('billedduration');
	$xmlData->AddColumn('originatingnumber');
	$xmlData->AddColumn('destinationnumber');
	$xmlData->AddColumn('lrndipfee');
	$xmlData->AddColumn('retailrate');
	$xmlData->AddColumn('cnamfee');
	$xmlData->AddColumn('retailprice');
	
	foreach($callrecordmasterDetailsInfo as $cdr){
		$rowData = array($cdr['callid'],$cdr['calltype'],$cdr['calldatetime'],$cdr['billedduration'],
						$cdr['originatingnumber'],$cdr['destinationnumber'],
						$cdr['lrndipfee'],$cdr['retailrate'],
						$cdr['cnamfee'],$cdr['retailprice']);
		$xmlData->AddRow($rowData);
	}
	
	echo $xmlData->ToXML();
}
else if($function == "DOWNLOAD"){
	header('Content-Type: application/csv'); 
	header('Content-Disposition: attachment; filename="cdr.csv"');
	
	$callrecordmasterDetails = new psql_callrecordmaster($connectstring);
	$callrecordmasterDetails->Connect();
	$callrecordmasterDetailsInfo = $callrecordmasterDetails->Select($customerid);
	$callrecordmasterDetails->Disconnect();
	
	//Data to csv
	$xmlData = new XMLTableData();
	$xmlData->AddColumn('callid');
	$xmlData->AddColumn('calltype');
	$xmlData->AddColumn('calldatetime');
	$xmlData->AddColumn('billedduration');
	$xmlData->AddColumn('originatingnumber');
	$xmlData->AddColumn('destinationnumber');
	$xmlData->AddColumn('lrndipfee');
	$xmlData->AddColumn('retailrate');
	$xmlData->AddColumn('cnamfee');
	$xmlData->AddColumn('retailprice');
	
	foreach($callrecordmasterDetailsInfo as $cdr){
		$rowData = array($cdr['callid'],$cdr['calltype'],$cdr['calldatetime'],$cdr['billedduration'],
						$cdr['originatingnumber'],$cdr['destinationnumber'],
						$cdr['lrndipfee'],$cdr['retailrate'],
						$cdr['cnamfee'],$cdr['retailprice']);
		$xmlData->AddRow($rowData);
	}
	
	echo $xmlData->ToCSV();
	
}
else if($function =="SELECTDATE")
{
	header('Content-Type: text/xml');
	$startdate = $_SERVER['HTTP_STARTDATE'];
	$enddate = $_SERVER['HTTP_ENDDATE'];
	
	$callrecordmasterDetails = new psql_callrecordmaster($connectstring);
	$callrecordmasterDetails->Connect();
	$callrecordmasterDetailsInfo = $callrecordmasterDetails->SelectDate($customerid, $startdate, $enddate);
	$callrecordmasterDetails->Disconnect();
	
	//Data to xml
	$xmlData = new XMLTableData();
	$xmlData->AddColumn('callid');
	$xmlData->AddColumn('calltype');
	$xmlData->AddColumn('calldatetime');
	$xmlData->AddColumn('billedduration');
	$xmlData->AddColumn('originatingnumber');
	$xmlData->AddColumn('destinationnumber');
	$xmlData->AddColumn('lrndipfee');
	$xmlData->AddColumn('retailrate');
	$xmlData->AddColumn('cnamfee');
	$xmlData->AddColumn('retailprice');
	
	foreach($callrecordmasterDetailsInfo as $cdr){
		$rowData = array($cdr['callid'],$cdr['calltype'],$cdr['calldatetime'],$cdr['billedduration'],
						$cdr['originatingnumber'],$cdr['destinationnumber'],
						$cdr['lrndipfee'],$cdr['retailrate'],
						$cdr['cnamfee'],$cdr['retailprice']);
		$xmlData->AddRow($rowData);
	}
	
	echo $xmlData->ToXML();
}
?>