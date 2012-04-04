<?php
/*viewlineitems.php*/
include '/lib/Page.php';
include 'config.php';
$db = pg_connect($connectstring);
?>
<?php
$javaScripts = <<< HEREDOC
<script type="text/javascript">
function confirmDelete(billingbatch, customerid, rowid){
	var agree=confirm("Are you sure you want to delete this row?");
	if (agree){
		window.location = "viewlineitems.php?batchid="+billingbatch+"&customerid="+customerid+"&delete="+rowid;
		return true ;
	}
	else{
		window.location = "viewlineitems.php?batchid="+billingbatch+"&customerid="+customerid;
		return false ;	
	}
}
</script>
HEREDOC;
?>
<?php
if(isset($_GET['modify'])){
	$modify = $_GET['modify'];
	$description = $_POST['description'];
	$amount = $_POST['amount'];
	$modifyStatement = "UPDATE billingbatchdetails 
						SET lineitemdesc='".$description."', lineitemamount='".$amount."'
						WHERE rowid = ".$modify.";";
	pg_query($modifyStatement);
}
else if(isset($_GET['delete'])){
	$delete = $_GET['delete'];
	$deleteStatement = "DELETE from billingbatchdetails
						WHERE rowid = ".$delete.";";
	pg_query($deleteStatement);
}
$customerid = $_GET['customerid'];
$billingbatchid = $_GET['batchid'];

$table = <<< HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Description</th>
<th>Amount</th>
<th>Date</th>
<th>Edit</th>
<th>Delete</th>
</tr>
</thead>
<tbody>
HEREDOC;
$allItems = array();
	$lineItemQuery = "SELECT lineitemdesc as \"description\", 
			lineitemamount as \"amount\",
			periodstartdate,
			periodenddate,
			rowid
			FROM billingbatchdetails
			WHERE customerid = '".$customerid."'
			AND billingbatchid = '".$billingbatchid."';";
	$lineItemQueryResults = pg_query($lineItemQuery);
	while($lineItem = pg_fetch_assoc($lineItemQueryResults)){
		$item = array("Date" => $lineItem['periodenddate']
					,"Billing Period" => $lineItem['periodstartdate'] ." to ". $lineItem['periodenddate']
					,"Description" => $lineItem['description']
					,"Amount" => $lineItem['amount']
					,"RowID" => $lineItem['rowid']
				);
		$allItems[] = $item;
	}
	foreach($allItems as $item){
$edit = '';	
if(isset($_GET['edit'])){
	$edit = $_GET['edit'];
}

if($item['RowID'] == $edit){
$table .= <<<HEREDOC
<tr>
<form action="viewlineitems.php?batchid={$billingbatchid}&customerid={$customerid}&modify={$item['RowID']}" method="POST">
<td><input type='text' value='{$item['Description']}' name='description'></td>
<td><input type='text' value={$item['Amount']} name='amount'/></td>
<td>{$item['Date']}</td>
<td class="actions" align="left">
<input type=Submit value=Submit />
</td>
<td></td>
</form>
</tr>\n
HEREDOC;
}
else{
$table .= <<<HEREDOC
<tr>
<td>{$item['Description']}</td>
<td>{$item['Amount']}</td>
<td>{$item['Date']}</td>
<td class="actions" align="center"><a href=viewlineitems.php?batchid={$billingbatchid}&customerid={$customerid}&edit={$item['RowID']} class="italic payment">Edit</a></td>
<td class="actions" align="center"><a href=javascript:confirmDelete("{$billingbatchid}","{$customerid}","{$item['RowID']}") class="italic payment">Delete</a></td>
</tr>\n
HEREDOC;
}
	}
	
	$table .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="7"></td>
	    	</tr>
	    </tfoot>
		</table>';
pg_close($db);
?>

<?php echo GetPageHead("View Line Items", "viewbillbatchdetails.php?batchid=".$_GET['batchid'],$javaScripts);?>

<div id="body">

<?php echo $table;?>

</div>
<?php echo GetPageFoot();?>