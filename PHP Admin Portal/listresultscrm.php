<?php
include 'lib/Page.php';
include 'config.php'; 

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr> 
<th>CallID</th> 
<th>CustomerID</th>
<th>CallType</td> 
<th>CallDateTime</th>
<th>Duration</th>
<th>BilledDuration</th>
<th>Direction</th>
<th>SourceIP</th>
<th>OriginationNumber</th> 
<th>DestinationNumber</th> 
<th>LRN</th>
<th>LRNDipFee</th>
<th>BilledNumber</th>
<th>BilledPrefix</th>
<th>RateDateTime</th>
<th>RetailRate</th>
<th>CNAMDipped</th>
<th>CNAMFee</th>
<th>BilledTier</th>
<th>RateCenter</th>
<th>BillingBatchID</th>
<th>RetailPrice</th>
<th>CarrierID</th>
</td></tr> 
</thead>
<tbody>
HEREDOC;

	$csv_output = "";
	$csv_hdr = "CallID|CustomerID|CallType|CallDateTime|Duration|BilledDuration|Direction|SourceIP|OriginationNumber|DestinationNumber|LRN|LRNDipFee|BilledNumber|BilledPrefix|RateDateTime|RetailRate|CNAMDipped|CNAMFee|BilledTier|RateCenter|BillingBatchID|RetailPrice|CarrierID";
	$csv_hdr .= "\n";

	$db = pg_connect($connectstring);

	$start = $_POST['start'];
	$end = $_POST['end'];

	$query = "SELECT * FROM callrecordmaster where cast(calldatetime as date) between '{$start}' and '{$end}'";
	
	$result = pg_query_params($db,'SELECT * FROM "callrecordmaster" where cast(calldatetime as date) between $1 and $2', array($start, $end));

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
		else{
			$myrow['calltype'] = 'Unknown';
		}
$htmltable .= <<<HEREDOC
<tr>
<td nowrap="nowrap">{$myrow['callid']}</td>
<td>{$myrow['customerid']}</td>
<td>{$myrow['calltype']}</td>
<td>{$myrow['calldatetime']}</td>
<td>{$myrow['duration']}</td>
<td>{$myrow['billedduration']}</td>
<td>{$myrow['direction']}</td>
<td>{$myrow['sourceip']}</td>
<td>{$myrow['originatingnumber']}</td>
<td>{$myrow['destinationnumber']}</td>
<td>{$myrow['lrn']}</td>
<td>{$myrow['lrndipfee']}</td>
<td>{$myrow['billednumber']}</td>
<td>{$myrow['billedprefix']}</td>
<td>{$myrow['rateddatetime']}</td>
<td>{$myrow['retailrate']}</td>
<td>{$myrow['cnamdipped']}</td>
<td>{$myrow['cnamfee']}</td>
<td>{$myrow['billedtier']}</td>
<td>{$myrow['ratecenter']}</td>
<td>{$myrow['billingbatchid']}</td>
<td>{$myrow['retailprice']}</td>
<td>{$myrow['carrierid']}</td>
</tr>\n
HEREDOC;

$csv_output .= $myrow['callid']. "|". $myrow['customerid']. "|". $myrow['calltype']. "|". $myrow['calldatetime']. "|". $myrow['duration']. "|". $myrow['billedduration']. "|". $myrow['direction']. "|". $myrow['sourceip']. "|". $myrow['originatingnumber']. "|". $myrow['destinationnumber']. "|". $myrow['lrn']. "|". $myrow['lrndipfee']. "|". $myrow['billednumber']. "|". $myrow['billedprefix']. "|". $myrow['rateddatetime']. "|". $myrow['retailrate']. "|". $myrow['cnamdipped']. "|". $myrow['cnamfee']. "|". $myrow['billedtier']. "|". $myrow['ratecenter']. "|". $myrow['billingbatchid']. "|". $myrow['retailprice']. "|". $myrow['carrierid']. "\n";
    
} 

$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="23"></td>
	    	</tr>
	    </tfoot>
		</table>';

?>
<?php echo GetPageHead("List CRM Results", "main.php")?>

<div id="body">

	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="CRMExport.csv" name="filename">
		<input type="hidden" value="1" name="quoted"/>
	</form>
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to SLINGER">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="SLINGERCRM.csv" name="filename">
		<input type="hidden" value="1" name="quoted"/>
	</form>
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to PIPE">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
		<input type="hidden" value="|" name="delimiter"/>
		<input type="hidden" value="CRMExport.csv" name="filename">
		<input type="hidden" value="1" name="quoted"/>
	</form>

	<?php echo $htmltable; ?>
	<br/>
	<br/>
</div>

<?php echo GetPageFoot("","");?>