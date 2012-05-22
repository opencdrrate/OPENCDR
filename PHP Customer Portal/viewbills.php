<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/session.php';
include_once $path . 'DAL/table_webportalaccesstokens.php';
include_once $path . 'DAL/table_webportalaccess.php';
include_once $path . 'DAL/table_billingbatchdetails.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

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
$table = '';
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

$billingDetails = new psql_billingbatchdetails($connectstring);
$billingDetails->Connect();
$billingDetailsInfo = $billingDetails->GetBatches($customerid);
$billingDetails->Disconnect();

/*
billingbatchid,totalamount,items
*/
$table .= <<< HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Billing ID</th>
<th>Start Date</th>
<th>End Date</th>
<th>Line Items</th>
<th>Total Billed Amount</th>
<th></th>
</tr>
</thead>
<tbody>
HEREDOC;
foreach($billingDetailsInfo as $bill){
	#add bill to table
	$table .= <<< HEREDOC
	<tr>
		<td>{$bill['billingbatchid']}</td>
		<td>{$bill['periodstartdate']}</td>
		<td>{$bill['periodenddate']}</td>
		<td>{$bill['items']}</td>
		<td>{$bill['totalamount']}</td>
		<td><a href="viewlineitems.php?token={$token}&billid={$bill['billingbatchid']}">View</a></td>
	</tr>
HEREDOC;
}
$table .= <<< HEREDOC
</tbody>

	    <tfoot>
	    	<tr>
		    <td colspan="13"></td>
	    	</tr>
	    </tfoot>
</table>
HEREDOC;
?>


<?php echo GetPageHead('Pending bills', 'main.php?token='.$token);?>
<div id='body'>
<?php echo $content;?><br>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>