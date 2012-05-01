<?php
include_once 'lib/Page.php';
include_once 'lib/session.php';
include_once 'vars/config.php';
include_once 'DAL/table_webportalaccesstokens.php';
include_once 'DAL/table_webportalaccess.php';
include_once 'DAL/table_paymentmaster.php';

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

$paymentDetails = new psql_paymentmaster($connectstring);
$paymentDetails->Connect();
$paymentDetailsInfo = $paymentDetails->Select($customerid);
$paymentDetails->Disconnect();

/*
paymentdate, paymentamount, paymenttype, paymentnote
*/
$table .= <<< HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>paymentdate</th>
<th>paymentamount</th>
<th>paymenttype</th>
<th>paymentnote</th>
</tr>
</thead>
<tbody>
HEREDOC;
foreach($paymentDetailsInfo as $bill){
	#add bill to table
	$table .= <<< HEREDOC
	<tr>
		<td>{$bill['paymentdate']}</td>
		<td>{$bill['paymentamount']}</td>
		<td>{$bill['paymenttype']}</td>
		<td>{$bill['paymentnote']}</td>
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


<?php echo GetPageHead('View Payments', 'main.php?token='.$token);?>
<div id='body'>
<?php echo $content;?><br>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>