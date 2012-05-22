<?php
	include_once 'config.php';

include_once $path . 'lib/Page.php';
include_once $path . 'lib/SQLQueryFuncs.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/localizer.php';

$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$db = pg_connect($connectstring);

if(isset($_GET['delete'])){

	$deleteid = $_GET['delete'];
	
	$deleteStatement = "DELETE FROM customermaster WHERE rowid=".$deleteid;
	pg_query($deleteStatement);
	echo ('<script type="text/javascript">');
	echo ('window.location = "listcustomers.php";');
	echo ('</script>');
}

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
<th>Customer Name</th>
<th>Billing Address</th>
<th>LRN Dip Rate</th>
<th>CNAM Dip Rate</th>
<th>Indeterminate Call Type</th>
<th>Billing Cycle</th>
<th>Total Billed</th>
<th>Payments</th>
<th>Balance</th>
<th></th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HEREDOC;

	$query = 'SELECT customermaster.customerid, customername, lrndiprate, cnamdiprate, "CallTypeDesc", billingcycle, coalesce("TotalCharges", 0) as "TotalCharges", customermaster.rowid FROM customermaster join vwcalltypes on (customermaster.indeterminatejurisdictioncalltype = vwcalltypes."CallType") left outer join vwcustomercharges on (customermaster.customerid = vwcustomercharges.customerid) order by customerid;';
	
	$csv_output = "";
	$csv_hdr = "CustomerID|CustomerName|LRNDipRate|CNAMDipRate|IndeterminateCallType|BillingCycle|TotalBilled|Balance";
	$csv_hdr .= "\n";

	$result = pg_query($query);
		if (!$result){ 
		echo "Problem with query ". $query ."<br/>";
		echo pg_last_error();
		exit();
	}

	while($myrow = pg_fetch_assoc($result)){

		$paymentQuery = "SELECT \"TotalPayments\" FROM vwcustomerpayments WHERE customerid ='".$myrow['customerid']."';";
		$paymentResults = pg_query($paymentQuery);
		$paymentData = pg_fetch_all($paymentResults);
		$totalPayments  = $paymentData[0]['TotalPayments'];
		if(count($totalPayments[0]) == 0){
			$totalPayments = 0;
	}

	$balance = ($myrow['TotalCharges'] - $totalPayments);

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['customername']}</td>
<td><a href=editcustomerbillingaddress.php?customerid={$myrow['customerid']}>Edit</a></td>
<td>{$myrow['lrndiprate']}</td>
<td>{$myrow['cnamdiprate']}</td>
<td>{$myrow['CallTypeDesc']}</td>
<td>{$myrow['billingcycle']}</td>
<td>{$region->FormatCurrency($myrow['TotalCharges'])}</td>
<td class="actions" align="center">
<a href=listpayments.php?customerid={$myrow['customerid']} class="italic payment">{$region->FormatCurrency($totalPayments)}</a>
</td><td>{$region->FormatCurrency($balance)}</td>
<td class="actions">
<a href=updatecustomer.php?rowid={$myrow['rowid']} class="btn-action update">Update</a></td>
<td class="actions">
<a href=javascript:confirmDelete({$myrow['rowid']}) class="btn-action delete">Delete</a></td>
</td>
<td>
<a href=customercontrolpanel.php?customerid={$myrow['customerid']}>Ctrl Panel</a>
</td>
</tr>\n
HEREDOC;

	    $csv_output .= $myrow['customerid']. "|". $myrow['customername']. "|". $myrow['lrndiprate']. "|". $myrow['cnamdiprate']. "|". $myrow['CallTypeDesc']. "|". $myrow['billingcycle']. "|". $myrow['TotalCharges']. "|". $balance. "\n";	
        } 

	$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="13"></td>
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
		window.location = "listcustomers.php?delete="+deleteid;
		return true;
	}
	else{
		window.location = "listcustomers.php";
		return false;	
	}
}
</script>
HEREDOC;
 ?>
	
	<?php echo GetPageHead("List Customers", "main.php", $javaScripts)?>
	
    <div id="body">
	<form action="addcustomer.php">
	<input type="submit" class="btn blue add-customer" value="Add Customer"> 
	</form>
	
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="CustomerExport.csv" name="filename">
	</form>

	<?php echo $htmltable; ?>
	<br/>
	<br/>
	<br/>
	
	</div>
	
<?php echo GetPageFoot("","");?>