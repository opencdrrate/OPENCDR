<?php
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';
	include 'config.php';
	include_once 'DAL/table_callrecordmaster_tbr.php';

	$query = 'SELECT * FROM callrecordmaster_tbr where calltype is null';
	
	
	$table = new psql_callrecordmaster_tbr($connectstring);
	$table->Connect();
	$numberOfRows = $table->CountUncat();
	
	$offset = 0;
	if(isset($_POST["offset"])){
		$offset = $_POST["offset"];
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
	   
	$titlespiped = "CallID,CustomerID,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDip,RateCenter,CarrierID";
	$titles = preg_split("/,/",$titlespiped,-1);
	
	#$queryResult = SQLSelectQuery($connectstring, $viewQuery, ",", "\n");
	
	$htmltable = AssocArrayToTable($allArrayResults,$titles); 
?> 



<?php echo GetPageHead("Uncategorized CDRs", "main.php"); ?>
<div id="body">
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
	
	<?php
			$limitOptions = <<< HEREDOC
		Showing rows : {$offset} to {$endoffset} <br>
		Total number of rows : {$numberOfRows}
		<br>
HEREDOC;
		if($offset > 0){
			$limitOptions .= '
			<form action="listresultsuncatcdr.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
			<input type="hidden" name="offset" value="'.$prevoffset.'">
			<input type="submit" value="View prev '.$limit.' results"/>
			</form>';
		}
		if($endoffset < $numberOfRows){
		$limitOptions .= '
		<form action="listresultsuncatcdr.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="offset" value="'.$endoffset.'">
		<input type="submit" value="View next '.$limit.' results"/>
		</form>';
		}
		echo $limitOptions;
			?>
	<?php echo $htmltable; ?>
</div>
<?php echo GetPageFoot();?>