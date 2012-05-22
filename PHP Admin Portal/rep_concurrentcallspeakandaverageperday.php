<?php

$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Date</th>
<th>Direction</th>
<th>Peak</th>
<th>Average</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "Date|Direction|Peak|Average";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select cast(calldatetime as date) as \"Date\", direction, max(concurrentcalls) as \"Peak\", avg(concurrentcalls) as \"Average\" from concurrentcallsdirection
                 group by cast(calldatetime as date), direction
                 order by cast(calldatetime as date), direction;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['Date']}</td>
<td>{$myrow['direction']}</td>
<td>{$myrow['Peak']}</td>
<td>{$myrow['Average']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['Date']. "|". $myrow['direction']. "|". $myrow['Peak']. "|". $myrow['Average']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="4"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Concurrent Calls - Inbound vs. Outbound Peak and Average per Day", "reports.php")?>
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