<?php
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';
	include 'config.php';
	include_once 'DAL/table_callrecordmaster_tbr.php';
	$message = '';
	$query = 'SELECT * FROM callrecordmaster_tbr where calltype is null';
	
	$table = new psql_callrecordmaster_tbr($connectstring);
	$table->Connect();
	
	if(isset($_GET['delete'])){
		$cdrList;
		if(isset($_POST['cdrList'])){
			$count = 0;
			$cdrList = $_POST['cdrList'];
			foreach($cdrList as $cdr){
				if($table->Delete(array('callid' => $cdr))){
					$count++;
				}
			}
			$message .= '<font color="red">'.$count . ' rows deleted.</font	><br>';
		}
		else{
		}
	}
	
	$numberOfRows = $table->CountUncat();
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	
	$limit = 1000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	$allArrayResults = $table->SelectUncat($offset, $limit);
	
	$viewQuery = 'SELECT callid, customerid, calldatetime, duration, direction, 
       sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
       ratecenter, carrierid
	   FROM callrecordmaster_tbr
	   where calltype is null';
	   
	
	#$queryResult = SQLSelectQuery($connectstring, $viewQuery, ",", "\n");
	
	$htmltable = <<< HEREDOC
	<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
	<thead><tr>
	<th></th>
	<th>CallID</th>
	<th>CustomerID</th>
	<th>CallDateTime</th>
	<th>Duration</th>
	<th>Direction</th>
	<th>SourceIP</th>
	<th>OriginationNumber</th>
	<th>DestinationNumber</th>
	<th>LRN</th>
	<th>CNAMDip</th>
	<th>RateCenter</th>
	<th>CarrierID</th>
	</tr></thead><tbody>
HEREDOC;
	$i = 0;
	foreach($allArrayResults as $row){
	/*callid, customerid, calldatetime, duration, direction, 
       sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
       ratecenter, carrierid
	*/
		$htmltable .= <<< HEREDOC
		<tr>
			<td><Input type="checkbox" name="cdrList[{$i}]" value="{$row['callid']}"/></td>
			<td>{$row['callid']}</td>
			<td>{$row['customerid']}</td>
			<td>{$row['calldatetime']}</td>
			<td>{$row['duration']}</td>
			<td>{$row['direction']}</td>
			<td>{$row['sourceip']}</td>
			<td>{$row['originatingnumber']}</td>
			<td>{$row['destinationnumber']}</td>
			<td>{$row['lrn']}</td>
			<td>{$row['cnamdipped']}</td>
			<td>{$row['ratecenter']}</td>
			<td>{$row['carrierid']}</td>
		</tr>
HEREDOC;
		$i++;
	}
	$htmltable .= <<< HEREDOC
	</tbody><tfoot><tr>
	<td colspan="15"></td>
	</tr></tfoot></table>
HEREDOC;
	/*
	$titlespiped = "CallID,CustomerID,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDip,RateCenter,CarrierID";
	$titles = preg_split("/,/",$titlespiped,-1);
	
	$htmltable = AssocArrayToTable($allArrayResults,$titles); 
	*/
	$javascripts = <<< HEREDOC
	<script type="text/javascript">
function confirmDelete(){
	var agree=confirm("Are you sure you want to delete these CDRs?");
	if (agree){
		document.forms["cdrDeleteAction"].submit();
		return true;
	}
	else{
	}
}
</script>
HEREDOC;
?> 



<?php echo GetPageHead("Uncategorized CDRs", "main.php", $javascripts); ?>

<div id="body">
	<?php echo $message;?>
	<form name="export" action="exportpipe.php" method="post">
		<input type="submit" class="btn orange export" value="Export Table">
		<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
		<input type="hidden" name="filename" value="UncatCDRExport.csv">
	</form>
	<form action="SQLExecuteAndRedirectToPage.php" method="post">
		<input type="hidden" name="page" value="main.php"/>
		<input type="hidden" name="connectString" value="<?php echo $connectstring; ?>"/>
		<input type="hidden" name="sqlStatement" value="select &quot;fnCategorizeCDR&quot;();"/>
		<input type="submit" class="btn blue add-customer" value="Categorize CDR"/>
	</form>
	<button type="button" onclick="confirmDelete()" id="deleteButton">Delete Selected CDRs</button><br>
		
	<?php
			$limitOptions = <<< HEREDOC
		Showing rows : {$offset} to {$endoffset} <br>
		Total number of rows : {$numberOfRows}
		<br>
HEREDOC;
		if($offset > 0){
			$limitOptions .= <<< HEREDOC
			<a href="listresultsuncatcdr.php?offset={$prevoffset}"><<< View prev {$limit} results</a>
HEREDOC;
		}
		if($endoffset < $numberOfRows){
		$limitOptions .= <<< HEREDOC
			<a href="listresultsuncatcdr.php?offset={$endoffset}">View next {$limit} results >>></a>
HEREDOC;
		}
		echo $limitOptions;?>
	
	<form action="listresultsuncatcdr.php?delete=1" method="POST" name="cdrDeleteAction">
		<?php echo $htmltable; ?>
	</form>
</div>
<?php echo GetPageFoot();?>