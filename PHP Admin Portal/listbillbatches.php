<?php

include 'lib/Page.php';
include 'config.php';
include 'lib/SQLQueryFuncs.php';

function CreateViewButton($batchid){
	$button = 
				'<form method="GET" action="viewbillbatchdetails.php">
					<input type="hidden" value="'.$batchid.'" name="batchid"/>
					<input type="submit" value="View"/>
				</form>';
	return $button;
}

function BuildTable($queryResult){
	$table = <<<HEREDOC
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

	$rows = preg_split('/\n/', $queryResult);
	$i = 1;
	foreach($rows as $row){
		$table .= "<tr>";
		$entries = preg_split('/,/', $row);
		
		$batchid = 0;
		$j = 1;
		if($i == 1){
			$i++;
			continue;
		}
		foreach($entries as $entry){
			if($j == 1){
				$batchid = $entry;
			}
			$table .= "<td>";
			$table .= $entry;
			$table .= "</td>";
			$j++;
		}
		$table .= "<td>";
		$table .= CreateViewButton($batchid);
		$table .= "</td>";

		$table .= "<td class='actions'>";
		$table .= "<a href=javascript:confirmDelete('{$batchid}') class=\"btn-action delete\">Delete</a>";
		$table .= "</td>";

		$table .= "</tr>";
		$i++;
	}
	$table .= <<<HEREDOC
			</tbody><tfoot><tr>
			<td colspan="6"></td>
			</tr></tfoot></table>
HEREDOC;
	return $table;
}

if(isset($_GET['deleteid'])){
	$id = $_GET['deleteid'];

	$db = pg_connect($connectstring);
	
	$deleteStatement = "select \"fnDeleteBillingBatch\"('$id');";
	pg_query($deleteStatement);
}

$fullQuery = 'SELECT billingbatchid, billingdate, billingcycleid, usageperiodend FROM billingbatchmaster;';
$queryResult = SQLSelectQuery($connectstring, $fullQuery, ",", "\n");
$table = BuildTable($queryResult);

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
<?php echo $table; ?>
<br/>
<br/>
</div>
</body>
<?php echo GetPageFoot();?>