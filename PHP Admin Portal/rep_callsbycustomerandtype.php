<?php

include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/localizer.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	$locale = $manager->GetSetting('region');
	$region = new localizer($locale);
	
$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
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
	$csv_hdr = "CustomerID|CallType|Calls|RawDuration|BilledDuration|LRNFees|CNAMFees|UsageFees|TotalFees";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = 'select customerid, calltype, count(callid) as "Calls", sum(duration) / 60 as
		 "Raw Duration", sum(billedduration) / 60 as "Billed Duration", sum(lrndipfee) as
		 "LRN Fees", sum(cnamfee) as "CNAM Fees", sum(retailprice - lrndipfee -
		 cnamfee) as "Usage Fees", sum(retailprice) as "Total Fees" from callrecordmaster group by customerid,
		 calltype order by customerid, calltype;';
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {
		$callType = $myrow['calltype'];
		if($callType == '5'){
			$myrow['calltype'] = 'Intrastate';
		}
		else if($callType == '10'){
			$myrow['calltype'] = 'Interstate';
		}
		else if($callType == '15'){
			$myrow['calltype'] = 'Tiered Origination';
		}
		else if($callType == '20'){
			$myrow['calltype'] = 'Termination of Indeterminate Jurisdiction';
		}
		else if($callType == '25'){
			$myrow['calltype'] = 'International';
		}
		else if($callType == '30'){
			$myrow['calltype'] = 'Toll-free Origination';
		}
		else if($callType == '35'){
			$myrow['calltype'] = 'Simple Termination';
		}
		else if($callType == '40'){
			$myrow['calltype'] = 'Toll-free Termination';
		}
		else{
			$myrow['calltype'] = 'Unknown';
		}
$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['calltype']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['Raw Duration']}</td>
<td>{$myrow['Billed Duration']}</td>
<td>{$region->FormatCurrency($myrow['LRN Fees'])}</td>
<td>{$region->FormatCurrency($myrow['CNAM Fees'])}</td>
<td>{$region->FormatCurrency($myrow['Usage Fees'])}</td>
<td>{$region->FormatCurrency($myrow['Total Fees'])}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['customerid']. "|". $myrow['calltype']. "|". $myrow['Calls']. "|". $myrow['Raw Duration']. "|". $myrow['Billed Duration']. "|". $myrow['LRN Fees']. "|". $myrow['CNAM Fees']. "|". $myrow['Usage Fees']. "|". $myrow['Total Fees']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="9"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Calls by Customer and Type", "reports.php")?>
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