<?php
	include_once 'config.php';

include_once $path . 'DAL/table_billingbatchmaster.php';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/SQLQueryFuncs.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/localizer.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$table = new psql_billingbatchmaster($connectstring);
$table->Connect();

function CreateViewButton($batchid){
	$button = 
				'<form method="GET" action="viewbillbatchdetails.php">
					<input type="hidden" value="'.$batchid.'" name="batchid"/>
					<input type="submit" value="View"/>
				</form>';
	return $button;
}

if(isset($_GET['deleteid'])){
	$id = $_GET['deleteid'];

	#$db = pg_connect($connectstring);
	
	#$deleteStatement = "select \"fnDeleteBillingBatch\"('$id');";
	#pg_query($deleteStatement);
	$table->Delete(array('billingbatchid' => $id));
}
$allRows = $table->SelectAll();
$table->Disconnect();
	$htmlTable = <<<HEREDOC
	<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
	<th>BillingBatchID</th>
	<th>BillingDate</th>
	<th>BillingCycleID</th>
	<th>UsagePeriodEnd</th>
	<th>View</th>
	<th></th>
	</tr>
	</thead>
	<tbody>
HEREDOC;

foreach($allRows as $row){
	$viewButton = CreateViewButton($row['billingbatchid']);
	$deleteButton = <<< HEREDOC
	<br><a href=javascript:confirmDelete('{$row['billingbatchid']}') class="btn-action delete">Delete</a>
HEREDOC;
	$htmlTable .= <<<HEREDOC
	<tr>
		<td>{$row['billingbatchid']}</td>
		<td>{$region->FormatDate($row['billingdate'])}</td>
		<td>{$row['billingcycleid']}</td>
		<td>{$region->FormatDate($row['usageperiodend'])}</td>
		<td>{$viewButton}</td>
		<td>{$deleteButton}</td>
	</tr>
HEREDOC;
}
$htmlTable .= <<<HEREDOC
			</tbody><tfoot><tr>
			<td colspan="6"></td>
			</tr></tfoot></table>
HEREDOC;

if(isset($_POST["export"])){
	$filepath = $_POST["filename"];
	
	$queryResult = SQLSelectQuery($connectstring, $fullQuery, ",", "\r\n"); 
	SaveQueryResultsToCSV($connectstring, $queryResult, $filepath);
	header('location: '.$filepath);
}
?>
<?php

$javaScripts = <<< HEREDOC
<script type="text/javascript">
function confirmDelete(deleteid){
	var agree=confirm("Are you sure you want to delete this batch?");
	if (agree){
		window.location = "listbillbatches.php?deleteid="+deleteid;
		return true;
	}
	else{
		window.location = "listbillbatches.php";
		return false;
	}
}
</script>
HEREDOC;
?>
<?php echo GetPageHead("List Billing Batches", "main.php", $javaScripts);?>
<body>
<div id="body">
<form action="listbillbatches.php" method="post">
	<input type="hidden" name="filename" value="BillingBatches.csv"/>
	<input type="hidden" name="export" value="1"/>
	<input type="submit" class="btn orange export" value="Export to CSV"/>
</form>
<form action="generatebillingbatch.php">
	<input type="submit" class="btn blue add-customer" value="Generate Billing Batch"/>
</form>
<?php echo $htmlTable; ?>
<br/>
<br/>
</div>
</body>
<?php echo GetPageFoot();?>