<?php
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';
	include 'config.php';

	$query = 'SELECT * FROM callrecordmaster_tbr where calltype is null';
	$viewQuery = 'SELECT callid, customerid, calldatetime, duration, direction, 
       sourceip, originatingnumber, destinationnumber, lrn, cnamdipped, 
       ratecenter, carrierid
	   FROM callrecordmaster_tbr
	   where calltype is null';
	   
	$titlespiped = "CallID,CustomerID,CallType,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDip,RateCenter,CarrierID";
	
	$titles = preg_split("/,/",$titlespiped,-1);
	$queryResult = SQLSelectQuery($connectstring, $viewQuery, ",", "\n");
	$htmltable = QueryResultToTable($queryResult, ",",$titles);  
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
	<?php echo $htmltable; ?>
</div>
<?php echo GetPageFoot();?>