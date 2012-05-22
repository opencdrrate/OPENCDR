<?php

include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
<th>Year</th>
<th>Month</th>
<th>CallType</th>
<th>Calls</th>
<th>RawDuration</th>
<th>BilledDuration</th>
<th>LRNFees</th>
<th>CNAMFees</th>
<th>UsageFees</th>
<th>TotalFees</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "CustomerID|Year|Month|CallType|Calls|RawDuration|BilledDuration|LRNFees|CNAMFees|UsageFees|TotalFees";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select customerid, date_part('year', calldatetime) as \"Year\", date_part('month', calldatetime) as \"Month\", calltype, count(callid) as \"Calls\", sum(duration) / 60 as
                 \"Raw Duration\", sum(billedduration) / 60 as \"Billed Duration\", sum(lrndipfee) as
                 \"LRN Fees\", sum(cnamfee) as \"CNAM Fees\", sum(retailPrice - lrndipfee - cnamfee) as \"Usage Fees\", sum(retailprice) as \"Total Fees\" from callrecordmaster group by customerid, date_part('year', calldatetime), date_part('month', calldatetime),
                 calltype order by customerid, date_part('year', calldatetime), date_part('month', calldatetime), calltype;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['Year']}</td>
<td>{$myrow['Month']}</td>
<td>{$myrow['calltype']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['Raw Duration']}</td>
<td>{$myrow['Billed Duration']}</td>
<td>{$myrow['LRN Fees']}</td>
<td>{$myrow['CNAM Fees']}</td>
<td>{$myrow['Usage Fees']}</td>
<td>{$myrow['Total Fees']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['customerid']. "|". $myrow['Year']. "|". $myrow['Month']. "|". $myrow['calltype']. "|". $myrow['Calls']. "|". $myrow['Raw Duration']. "|". $myrow['Billed Duration']. "|". $myrow['LRN Fees']. "|". $myrow['CNAM Fees']. "|". $myrow['Usage Fees']. "|". $myrow['Total Fees']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="11"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Calls by Customer and Type (Per Month)", "reports.php")?>
</head>

<div id="body">

	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="reportexport.csv" name="filename">
	</form>

	<?php echo $htmltable; ?>

</div>

<?php echo GetPageFoot("","");?>