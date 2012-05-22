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
	
	$deleteStatement = "DELETE FROM recurringchargemaster WHERE recurringchargeid=".$deleteid;
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
<th>RecurringChargeID</th> 
<th>CustomerID</th>
<th>ActivationDate</th> 
<th>DeactivationDate</th>
<th>UnitAmount</th>
<th>Quantity</th>
<th>ChargeDesc</th>
<th></th>
</tr> 
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "RecurringChargeID|CustomerID|ActivationDate|DeactivationDate|UnitAmount|Quantity|ChargeDesc";
	$csv_hdr .= "\n";

	$query = "SELECT * FROM recurringchargemaster where customerid = '$customerid';";
	
	$result = pg_query($db,$query);

	while($myrow = pg_fetch_assoc($result)) {
 
$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['recurringchargeid']}</td>
<td>{$myrow['customerid']}</td>
<td>{$region->FormatDate($myrow['activationdate'])}</td>
<td>{$region->FormatDate($myrow['deactivationdate'])}</td>
<td>{$region->FormatCurrency($myrow['unitamount'])}</td>
<td>{$myrow['quantity']}</td>
<td>{$myrow['chargedesc']}</td>
<td class="actions">
<a href=javascript:confirmDelete({$myrow['recurringchargeid']}) class="btn-action delete">Delete</a></td>
</td></tr>\n
HEREDOC;

$csv_output .= $myrow['recurringchargeid']. "|". $myrow['customerid']. "|". $myrow['activationdate']. "|". $myrow['deactivationdate']. "|". $myrow['unitamount']. "|". $myrow['quantity']. "|". $myrow['chargedesc']. "\n";
    
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
		window.location = "listcustomerrecurringcharges.php?delete="+deleteid;
		return true;
	}
	else{
		window.location = "listcustomerrecurringcharges.php";
		return false;	
	}
}
</script>
HEREDOC;
 ?>
<?php echo GetPageHead("Recurring Charges for $customerid", "rates.php", $javaScripts);?>

<body>
<div id="body">

	<form method="post" action="addcustomerrecurringcharge.php?customerid=<?php echo $_GET['customerid']; ?>">
		<input type="submit" class="btn blue add-customer" value="Add Recurring Charge"> 
	</form>

	<form name="export" action="exportpipe.php" method="post">
   		<input type="submit" class="btn orange export" value="Export table to Pipe">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="RecurringchargeExport.csv" name="filename">
	</form>

	<?php echo $htmltable; ?>
	<br/>
	<br/>
	</div>
	</body>
	<?php echo GetPageFoot("","");?>