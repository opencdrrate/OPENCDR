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
<th>OriginatingNumber</th>
<th>Calls</th>
<th>RawMinutes</th>
<th>BilledMinutes</th>
<th>RetailPrice</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "OriginatingNumber|Calls|RawMinutes|BilledMinutes|RetailPrice";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select originatingnumber, count(*) as \"Calls\", sum(duration)/ 60 as \"RawMinutes\" , sum(billedduration) / 60 as \"BilledMinutes\" , sum(retailprice) as \"RetailPrice\" from callrecordmaster
                 where direction = 'I'
		 and calldatetime between '2000-01-01' and '2012-12-31'
		 group by originatingnumber
		 order by sum(retailprice) desc;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['originatingnumber']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['RawMinutes']}</td>
<td>{$myrow['BilledMinutes']}</td>
<td>{$myrow['RetailPrice']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['originatingnumber']. $myrow['Calls']. "|". $myrow['RawMinutes']. "|". $myrow['BilledMinutes']. "|". $myrow['RetailPrice']. "\n";

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
<?php echo GetPageHead("Calls per DID - Incoming", "reports.php")?>
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