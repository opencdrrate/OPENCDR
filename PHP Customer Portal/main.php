<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/session.php';
include_once $path . 'DAL/table_customerbillingaddressmaster.php';
include_once $path . 'DAL/table_customermaster.php';
include_once $path . 'DAL/table_webportalaccesstokens.php';
include_once $path . 'DAL/table_webportalaccess.php';
include_once $path . 'DAL/table_billingbatchdetails.php';
include_once $path . 'DAL/table_paymentmaster.php';
include_once $path . 'DAL/table_callrecordmaster.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/localizer.php';

$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$region = $manager->GetSetting('region');

$locale = new localizer($region);

if(!isset($_GET['token'])){
	#You need to be logged in to view this page
	header('location: login.php?error=notloggedin');
}
$token = $_GET['token'];

if(IsTokenExpired($token, $connectstring)){
	header('location: login.php?error=notloggedin');
}
else{
	UpdateExpiry($token,$connectstring);
}

$content = <<< HEREDOC
	<a href="login.php?token={$token}&logout=1">logout</a>
HEREDOC;

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

$billingaddressmaster = new psql_customerbillingaddressmaster($connectstring);
$billingaddressmaster->Connect();
$billingaddressInfo = $billingaddressmaster->Select($customerid);
$billingaddressmaster->Disconnect();

$address = 'No Address!';
if(count($billingaddressInfo) > 0){
	$address = $billingaddressInfo[0]['address1'] . ', ' . $billingaddressInfo[0]['city'];
}

$customermaster = new psql_customermaster($connectstring);
$customermaster->Connect();
$customermasterInfo = $customermaster->Select($customerid);
$customermaster->Disconnect();

$name = 'No Name!';
if(count($customermasterInfo) > 0){
	$name = $customermasterInfo['customername'];
}
$billingDetails = new psql_billingbatchdetails($connectstring);
$billingDetails->Connect();
$billingDetailsInfo = $billingDetails->GetBatches($customerid);
$billingDetails->Disconnect();

$totalBilled = 0;
foreach($billingDetailsInfo as $detail){
	$totalBilled += $detail['totalamount'];
}
$totalBilledLabel = $locale->FormatCurrency($totalBilled);
$paymentDetails = new psql_paymentmaster($connectstring);
$paymentDetails->Connect();
$paymentDetailsInfo = $paymentDetails->Select($customerid);
$paymentDetails->Disconnect();

$totalPaid = 0;
foreach($paymentDetailsInfo as $bill){
	$totalPaid += floatval($bill['paymentamount']);
}
$totalPaidLabel = $locale->FormatCurrency($totalPaid);
$callrecordmaster = new psql_callrecordmaster($connectstring);
$callrecordmaster->Connect();
$callrecordmasterInfo = $callrecordmaster->Select($customerid);
$callrecordmaster->Disconnect();

$CDRCount = count($callrecordmasterInfo);
$balanceAmount = $totalBilled - $totalPaid;
$balance_label = $balanceAmount < 0 ? 'Prepaid Balance' : 'Balance';
$balance = $locale->FormatCurrency(abs($balanceAmount));
$table = '';
$table .= <<< HEREDOC

<table id="listing-table" cellpadding="0" cellspacing="0">	
		<tr>
		<td>Name: </td><td>{$name}</td>
		</tr>
		<tr>
		<td>Address: </td><td>{$address}</td>
		</tr>
		<tr>
		<td>Total Billed: </td><td><a href="viewbills.php?token={$token}">{$totalBilledLabel}</a></td>
		</tr>
		<tr>
		<td>Payments Received: </td><td><a href="viewpayments.php?token={$token}">{$totalPaidLabel}</a></td>
		</tr>
		<tr>
		<td>{$balance_label}</td><td>{$balance}</td>
		</tr>
		<tr>
		<td>CDR: </td><td><a href="viewcdr.php?token={$token}">{$CDRCount}</a></td>
		</tr>
</table>
HEREDOC;

?>
<?php echo GetPageHead('Main', '');?>
<div id='body'>
<?php echo $content;?><br>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>