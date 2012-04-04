<?php

	include 'lib/Page.php';
	include 'config.php';

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Year</th>
<th>Month</th>
<th>CarrierID</th>
<th>Direction</th>
<th>Calls</th>
<th>RawDuration</th>
<th>BilledDuration</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "Year|Month|CarrierID|Direction|Calls|RawDuration|BilledDuration";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = 'select * from vwcallspermonthpercarrier order by "Year", "Month", carrierid, direction;';
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['Year']}</td>
<td>{$myrow['Month']}</td>
<td>{$myrow['carrierid']}</td>
<td>{$myrow['direction']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['RawDuration']}</td>
<td>{$myrow['BilledDuration']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['Year']. "|". $myrow['Month']. "|". $myrow['carrierid']. "|". $myrow['direction']. "|". $myrow['Calls']. "|". $myrow['RawDuration']. "|". $myrow['BilledDuration']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="7"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Calls Per Month, Carrier", "reports.php")?>
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