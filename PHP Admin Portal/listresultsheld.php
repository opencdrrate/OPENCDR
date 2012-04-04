<?php

include 'lib/Page.php';
include 'config.php'; 

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
      
	include 'config.php';

	$db = pg_connect($connectstring);

	$query = 'SELECT * FROM callrecordmaster_held;';
	#$result = pg_query_params($db,'SELECT * FROM "callrecordmaster_held" where cast(calldatetime as date) between $1 and $2', array($start, $end));

	$result = pg_query($query);

	$csv_output = "";
	$csv_hdr = "CallID|CustomerID|IndeterminateCallType|CallDateTime|Duration|Direction|SourceIP|OriginationNumber|DestinationNumber|LRN|CNAMDipRate|RateCenter|CarrierID|ErrorMessage";
	$csv_hdr .= "\n";

	        while($myrow = pg_fetch_assoc($result)) { 

           $htmltable .= <<<HEREDOC
<tr><td>{$myrow['callid']}</td><td>{$myrow['customerid']}</td><td>{$myrow['calltype']}</td><td>{$myrow['calldatetime']}</td><td>{$myrow['duration']}</td><td>{$myrow['direction']}</td><td>{$myrow['sourceip']}</td><td>{$myrow['originatingnumber']}</td><td>{$myrow['destinationnumber']}</td><td>{$myrow['lrn']}</td><td>{$myrow['cnamdipped']}</td><td>{$myrow['ratecenter']}</td><td>{$myrow['carrierid']}</td><td>{$myrow['errormessage']}</td></tr>\n
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

?>

<?php echo GetPageHead("Rating Errors","main.php");?>
<div id="body">
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
    	<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
    	<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
	<input type="hidden" value="HELDExport" name="filename">
	</form>
	<?php echo $htmltable; ?>
</div>
	<?php echo GetPageFoot();?>