
<?php
    include 'config.php'; 
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';
$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Process Name</th>
<th>Start Date Time</th>
<th>End Date Time</th>
<th>Duration</th>
<th>Records</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$db = pg_connect($connectstring);

        $query = 'SELECT processname,startdatetime,enddatetime,duration,records FROM processhistory order by "enddatetime";'; 
		$queryResult = SQLSelectQuery($connectstring, $query, ",", "\n");
		$titles = preg_split("/,/","Process Name,Start Time,End Time,Duration,Records",-1);
		$htmltable = QueryResultToTable($queryResult, ",",$titles);
        ?> 

	<?php echo GetPageHead("List Process History");?>
	<div id="body">
    <form name="export" action="exportpipe.php" method="post">
		<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
		<input type="hidden" name="filename" value="ProcessExport.csv">
	</form>
	<?php echo $htmltable; ?>
	<br/>
	<br/>
	</div>
	<?php echo GetPageFoot();?>