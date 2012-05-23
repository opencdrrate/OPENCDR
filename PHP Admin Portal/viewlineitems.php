<?php
/*viewlineitems.php*/
include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/localizer.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'DAL/table_billingbatchdetails.php';

$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$detailsTable = new psql_billingbatchdetails($connectstring);
$detailsTable->Connect();
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
	$detailsTable->Update(array('rowid' => $modify),array('lineitemdesc'=> $description, 'lineitemamount'=>$amount));
}
else if(isset($_GET['delete'])){
	$delete = $_GET['delete'];
	$detailsTable->Delete(array('rowid' => $delete));
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
$allItems = $detailsTable->SelectCustomerBatch($customerid,$billingbatchid);
	foreach($allItems as $item){
		$edit = '';	
		if(isset($_GET['edit'])){
			$edit = $_GET['edit'];
		}

		if($item['rowid'] == $edit){
		$table .= <<<HEREDOC
		<tr>
		<form action="viewlineitems.php?batchid={$billingbatchid}&customerid={$customerid}&modify={$item['rowid']}" method="POST">
		<td><input type='text' value='{$item['lineitemdesc']}' name='description'></td>
		<td><input type='text' value={$item['lineitemamount']} name='amount'/></td>
		<td>{$region->FormatDate($item['periodstartdate'])}</td>
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
		<td>{$item['lineitemdesc']}</td>
		<td>{$region->FormatCurrency($item['lineitemamount'])}</td>
		<td>{$region->FormatDate($item['periodstartdate'])}</td>
		<td class="actions" align="center"><a href=viewlineitems.php?batchid={$billingbatchid}&customerid={$customerid}&edit={$item['rowid']} class="italic payment">Edit</a></td>
		<td class="actions" align="center"><a href=javascript:confirmDelete("{$billingbatchid}","{$customerid}","{$item['rowid']}") class="italic payment">Delete</a></td>
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
$detailsTable->Disconnect();
?>

<?php echo GetPageHead("View Line Items", "viewbillbatchdetails.php?batchid=".$_GET['batchid'],$javaScripts);?>

<div id="body">

<?php echo $table;?>

</div>
<?php echo GetPageFoot();?>