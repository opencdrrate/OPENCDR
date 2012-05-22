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
<th>CarrierID</th>
<th>RateCenter</th>
<th>Calls</th>
<th>RawDuration</th>
<th>BilledDuration</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "CarrierID|RateCenter|Calls|RawDuration|BilledDuration";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select carrierid, ratecenter, cast(avg(\"Calls\") as bigint) as \"Calls\", avg(\"RawDuration\") as \"RawDuration\", avg(\"BilledDuration\") as \"BilledDuration\" from vwcallspermonthpercarrierratecenter where direction = 'I'
		 group by carrierid, ratecenter
		 order by carrierid, ratecenter;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['carrierid']}</td>
<td>{$myrow['ratecenter']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['RawDuration']}</td>
<td>{$myrow['BilledDuration']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['carrierid']. "|". $myrow['ratecenter']. "|". $myrow['Calls']. "|". $myrow['RawDuration']. "|". $myrow['BilledDuration']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="5"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Average Monthly Inbound Calls Per Carrier, RateCenter", "reports.php")?>
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