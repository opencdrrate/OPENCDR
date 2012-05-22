<?php

include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

if (isset($_POST['submit'])) {

$start = pg_escape_string($_POST['start']);
$end = pg_escape_string($_POST['end']);

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>SourceIP</th>
<th>Calls</th>
<th>RawMinutes</th>
<th>BilledMinutes</th>
<th>RetailPrice</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "SourceIP|Calls|RawMinutes|BilledMinutes|RetailPrice";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$query = "select sourceip, count(*) as \"Calls\", sum(duration) / 60 as \"RawMinutes\" , sum(billedduration) / 60 as \"BilledMinutes\" , sum(retailprice) as \"RetailPrice\" from callrecordmaster
                 where calldatetime between '$start' and '$end'
                 group by sourceip
                 order by sourceip;";
	
	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['sourceip']}</td>
<td>{$myrow['Calls']}</td>
<td>{$myrow['RawMinutes']}</td>
<td>{$myrow['BilledMinutes']}</td>
<td>{$myrow['RetailPrice']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['sourceip']. "|". $myrow['Calls']. "|". $myrow['RawMinutes']. "|". $myrow['BilledMinutes']. "|". $myrow['RetailPrice']. "\n";

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
	<?php echo GetPageHead("Calls per IP Address - Results", "rep_callsperipaddress.php");?>
	</head>
	<div id="body">

	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
	<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
	<input type="hidden" value="reportexport.csv" name="filename">
	</form>
	<?php echo $htmltable;?>
	</div>

	<?php echo GetPageFoot();?>
<?php
}
else {
?>

	<head>
	<?php echo GetPageHead("Calls per IP Address", "reports.php");?>
	</head>
	<div id="body">

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="searchform">
	<label>Start: </label><input type="text" name="start" value="<?php if (isset($_POST['submit'])) { echo $start; }?>"/><br />
	<label>End:   </label><input type="text" name="end" value="<?php if (isset($_POST['submit'])) { echo $end; }?>"/><br />
	<input type="submit" name="submit" value="Submit" />
	</form>
	</div>
	<?php echo GetPageFoot();?>

<?php
	
}
?>