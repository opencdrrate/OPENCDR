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
<th>CarrierID</th>
<th>Year</th>
<th>Month</th>
<th>InboundCalls</th>
<th>InboundRawDuration</th>
<th>InboundBilledDuration</th>
<th>OutboundCalls</th>
<th>OutboundRawDuration</th>
<th>OutboundBilledDuration</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "CustomerID|CarrierID|Year|Month|InboundCalls|InboundRawDuration|InboundBilledDuration|OutboundCalls|OutboundRawDuration|OutboundBilledDuration";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select customerid, carrierid, date_part('year', calldatetime) as \"Year\", date_part('month', calldatetime) as \"Month\", sum(\"InboundCall\") as \"InboundCalls\", sum(\"InboundDuration\") / 60 as \"InboundRawDuration\", sum(\"InboundBilledDuration\") / 60 as \"InboundBilledDuration\", 
		 sum(\"OutboundCall\") as \"OutboundCalls\", sum(\"OutboundDuration\") / 60 as \"OutboundRawDuration\", sum(\"OutboundBilledDuration\") / 60 as \"OutboundBilledDuration\" from vwcalldirection 
		 group by customerid, carrierid, date_part('year', calldatetime), date_part('month', calldatetime)
		 order by customerid, carrierid, date_part('year', calldatetime), date_part('month', calldatetime);";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['carrierid']}</td>
<td>{$myrow['Year']}</td>
<td>{$myrow['Month']}</td>
<td>{$myrow['InboundCalls']}</td>
<td>{$myrow['InboundRawDuration']}</td>
<td>{$myrow['InboundBilledDuration']}</td>
<td>{$myrow['OutboundCalls']}</td>
<td>{$myrow['OutboundRawDuration']}</td>
<td>{$myrow['OutboundBilledDuration']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['customerid']. "|". $myrow['carrierid']. "|" .$myrow['Year']. "|". $myrow['Month']. "|". $myrow['InboundCalls']. "|". $myrow['InboundRawDuration']. "|". $myrow['InboundBilledDuration']. "|". $myrow['OutboundCalls']. "|". $myrow['OutboundRawDuration']. "|". $myrow['OutboundBilledDuration']. "\n";

} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="10"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<head>
<?php echo GetPageHead("Inbound vs. Outbound Minutes Per Customer, Carrier, Month", "reports.php")?>
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