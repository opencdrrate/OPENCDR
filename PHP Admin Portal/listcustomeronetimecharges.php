<?php

include 'lib/Page.php';
include 'config.php';
include 'lib/SQLQueryFuncs.php'; 

$db = pg_connect($connectstring);

if(isset($_GET['delete'])){
	$deleteid = $_GET['delete'];
	
	$deleteStatement = "DELETE FROM onetimechargequeue WHERE onetimechargeid=".$deleteid;
	pg_query($deleteStatement);
	echo ('<script type="text/javascript">');
	echo ('window.location = "rates.php";');
	echo ('</script>');
}

if(isset($_GET['customerid'])){
	$customerid = $_GET['customerid'];
}

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr> 
<th>OneTimeChargeID</th> 
<th>CustomerID</th>
<th>ChargeDate</th> 
<th>UnitAmount</th>
<th>Quantity</th>
<th>ChargeDesc</th>
<th>BillingBatchID</th>
<th></th>
</tr> 
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "OneTimeChargeID|CustomerID|ChargeDate|UnitAmount|Quantity|ChargeDesc|BillingBatchID";
	$csv_hdr .= "\n";

	$query = "SELECT * FROM onetimechargequeue where customerid = '$customerid';";
	
	$result = pg_query($db,$query);

	while($myrow = pg_fetch_assoc($result)) {
 
$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['onetimechargeid']}</td>
<td>{$myrow['customerid']}</td>
<td>{$myrow['chargedate']}</td>
<td>{$myrow['unitamount']}</td>
<td>{$myrow['quantity']}</td>
<td>{$myrow['chargedesc']}</td>
<td>{$myrow['billingbatchid']}</td>
<td class="actions">
<a href=javascript:confirmDelete({$myrow['onetimechargeid']}) class="btn-action delete">Delete</a></td>
</td></tr>\n
HEREDOC;

$csv_output .= $myrow['onetimechargeid']. "|". $myrow['customerid']. "|". $myrow['chargedate']. "|". $myrow['unitamount']. "|". $myrow['quantity']. "|". $myrow['chargedesc']. "|". $myrow['billingbatchid']. "\n";
    
} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="8"></td>
	    	</tr>
	    </tfoot>
		</table>';

        ?>	 

<?php

$javaScripts = <<< HEREDOC
<script type="text/javascript">
function confirmDelete(deleteid){
	var agree=confirm("Are you sure you want to delete this row?");
	if (agree){
		window.location = "listcustomeronetimecharges.php?delete="+deleteid;
		return true;
	}
	else{
		window.location = "listcustomeronetimecharges.php";
		return false;	
	}
}
</script>
HEREDOC;
 ?>
<?php echo GetPageHead("One Time Charges for $customerid", "rates.php", $javaScripts);?>

<body>
<div id="body">

	<form method="post" action="addcustomeronetimecharge.php?customerid=<?php echo $_GET['customerid']; ?>">
		<input type="submit" class="btn blue add-customer" value="Add One-time Charge"> 
	</form>

	<form name="export" action="exportpipe.php" method="post">
   		<input type="submit" class="btn orange export" value="Export table to Pipe">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="OnetimeChargeExport.csv" name="filename">
	</form>
	<br/>
	<h4 style="font-style:italic;color:#0000FF">New one-time charges will be applied to the customer's account when their next bill is generated.</h4>
	<br/>
	<?php echo $htmltable; ?>
	<br/>
	<br/>
	</div>
	</body>
	<?php echo GetPageFoot("","");?>