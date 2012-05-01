<?php
include_once 'lib/Page.php';
include_once 'lib/session.php';
include_once 'vars/config.php';
include_once 'DAL/table_webportalaccesstokens.php';
include_once 'DAL/table_webportalaccess.php';
include_once 'DAL/table_callrecordmaster.php';

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

$callrecordmasterDetails = new psql_callrecordmaster($connectstring);
$callrecordmasterDetails->Connect();
$callrecordmasterDetailsInfo = $callrecordmasterDetails->Select($customerid);
$callrecordmasterDetails->Disconnect();

/*
callid, calltype, calldatetime, billedduration, 
       originatingnumber, destinationnumber,  
       lrndipfee, retailrate,cnamfee, retailprice
*/
$table .= <<< HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>callid</th>
<th>calltype</th>
<th>calldatetime</th>
<th>billedduration</th>
<th>originatingnumber</th>
<th>destinationnumber</th>
<th>lrndipfee</th>
<th>retailrate</th>
<th>cnamfee</th>
<th>retailprice</th>
</tr>
</thead>
<tbody>
HEREDOC;
foreach($callrecordmasterDetailsInfo as $cdr){
	#add cdr to table
	$table .= <<< HEREDOC
	<tr>
		<td>{$cdr['callid']}</td>
		<td>{$cdr['calltype']}</td>
		<td>{$cdr['calldatetime']}</td>
		<td>{$cdr['billedduration']}</td>
		<td>{$cdr['originatingnumber']}</td>
		<td>{$cdr['destinationnumber']}</td>
		<td>{$cdr['lrndipfee']}</td>
		<td>{$cdr['retailrate']}</td>
		<td>{$cdr['cnamfee']}</td>
		<td>{$cdr['retailprice']}</td>
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


<?php echo GetPageHead('View CDRs', 'main.php?token='.$token);?>
<div id='body'>
<?php echo $content;?><br>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>