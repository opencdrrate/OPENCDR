<?php

	include 'lib/Page.php';
	include 'config.php';

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
<th>calltype</th>
<th>Calls</th>
<th>RawMinutes</th>
<th>BilledMinutes</th>
<th>RetailPrice</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "CustomerID|CallType|Calls|RawMinutes|BilledMinutes|RetailPrice";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select customerid, calltype, count(*) as \"Calls\", sum(duration) / 60 as \"RawMinutes\" , sum(billedduration) / 60 as \"BilledMinutes\" , sum(retailprice) as \"RetailPrice\" from callrecordmaster
                 where calldatetime between '2000-01-01' and '2012-12-31'
                 group by customerid, calltype
                 order by customerid, calltype;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['calltype']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['RawMinutes']}</td>
<td>{$myrow['BilledMinutes']}</td>
<td>{$myrow['RetailPrice']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['customerid']. $myrow['calltype']. "|". $myrow['Calls']. "|". $myrow['RawMinutes']. "|". $myrow['BilledMinutes']. "|". $myrow['RetailPrice']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="6"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Calls per Customer and Type", "reports.php")?>
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