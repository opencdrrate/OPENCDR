<?php

include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'DAL/table_callrecordmaster_held.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/localizer.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$helpPage = <<< HEREDOC
	https://sourceforge.net/p/opencdrrate/home/Rating%20Errors/
HEREDOC;

if(isset($_GET['move'])){
	$db = pg_connect($connectstring);
	
	$moveString = 'SELECT "fnMoveHELDCDRToTBR"();';
	pg_query($db, $moveString);
	
	pg_close($db);
}

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<tr><thead>
<th>CallID</th>
<th>Customer ID</th>
<th>CallType</th>
<th>CallDate Time</th>
<th>Duration</th>
<th>Direction</th>
<th>SourceIP</th>
<th>OriginationNumber</th>
<th>DestinationNumber</th> 
<th>LRN</th>
<th>CNAMDipped</th>
<th>RateCenter</th>
<th>CarrierID</th>
<th>ErrorMessage</th>
</thead></tr>
HEREDOC;
	$query = 'SELECT * FROM callrecordmaster_held;';
	$table = new psql_callrecordmaster_held($connectstring);
	$table->Connect();
	$numberOfRows = $table->CountResults();
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	$limit = 1000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	
	$results = $table->SelectSubset($offset, $limit);
	$table->Disconnect();

	$csv_output = "";
	$csv_hdr = "CallID|CustomerID|IndeterminateCallType|CallDateTime|Duration|Direction|SourceIP|OriginationNumber|DestinationNumber|LRN|CNAMDipRate|RateCenter|CarrierID|ErrorMessage";
	$csv_hdr .= "\n";

	        foreach($results as $myrow) { 
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
	<td nowrap="nowrap">{$myrow['callid']}</td>
	<td>{$myrow['customerid']}</td>
	<td>{$myrow['calltype']}</td>
	<td>{$region->FormatDateTime($myrow['calldatetime'])}</td>
	<td>{$myrow['duration']}</td>
	<td>{$myrow['direction']}</td>
	<td>{$myrow['sourceip']}</td>
	<td>{$myrow['originatingnumber']}</td>
	<td>{$myrow['destinationnumber']}</td>
	<td>{$myrow['lrn']}</td>
	<td>{$myrow['cnamdipped']}</td>
	<td>{$myrow['ratecenter']}</td>
	<td>{$myrow['carrierid']}</td>
	<td><a href="{$helpPage}" target="blank">{$myrow['errormessage']}</a></td>
</tr>\n
HEREDOC;

	$csv_output .= $myrow['callid']. '|'. $myrow['customerid']. "|". $myrow['calltype']. "|". $myrow['calldatetime']. "|". $myrow['duration']. "|". $myrow['direction']. "|". $myrow['sourceip']. "|". $myrow['originatingnumber']. "|". $myrow['destinationnumber']. "|". $myrow['lrn']. "|". $myrow['cnamdipped']. "|". $myrow['ratecenter']. "|". $myrow['carrierid']. "|". $myrow['errormessage']. "\n";     
} 
		$htmltable .= '
	    <tfoot>
	    	<tr>
		    <td colspan="14"></td>
	    	</tr>
	    </tfoot>
		</table>';

		$limitOptions = <<< HEREDOC
			Showing rows : {$offset} to {$endoffset} <br>
			Total number of rows : {$numberOfRows}
			<br>
HEREDOC;
		if($offset > 0){
		$limitOptions .= <<< HEREDOC
		<a href="listresultsheld.php?offset={$prevoffset}"><<< View prev {$limit} results    </a>
HEREDOC;
		}
		if($endoffset < $numberOfRows){
		$limitOptions .= <<< HEREDOC
		<a href="listresultsheld.php?offset={$endoffset}">View next {$limit} results >>></a>
HEREDOC;
		}
?>

<?php echo GetPageHead("Rating Errors","main.php");?>
<div id="body">
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
    	<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
    	<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
	<input type="hidden" value="HELDExport" name="filename">
	</form>
	<form action="listresultsheld.php?move=1" method="post">
   	<input type="submit" class="btn blue add-customer" value="Move to Rating Queue"/>
	</form>
	<form action="<?php echo $helpPage;?>" method="post" target="_blank">
   	<input type="submit" class="btn orange export" value="HELP"/>
	</form>
	<?php echo $limitOptions;?>
	<?php echo $htmltable; ?>
</div>
	<?php echo GetPageFoot();?>