<?php
include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'DAL/table_paymentmaster.php';
include_once $path . 'lib/localizer.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$paymentTable = new psql_paymentmaster($connectstring);
$paymentTable->Connect();

function CreateDeleteButton($rowid, $customerid){
	$deleteButton = '<form action="listpayments.php?customerid='.$customerid.'" method="post">';
	$deleteButton .=	'<input type="hidden" name="function" value="delete"/>';
	$deleteButton .=	'<input type="hidden" name="customerid" value="'.$customerid.'" />';
	$deleteButton .=	'<input type="hidden" name="rowid" value="'.$rowid.'"/>';
	$deleteButton .= 	'<input type="submit" class="btn-action delete" value="delete">';
	$deleteButton .= '</form>';
	return $deleteButton;
}
if(isset($_POST['function'])){
	$function = $_POST['function'];
	if($function == "delete"){
		$customerid = $_POST['customerid'];
		$rowid = $_POST['rowid'];
		$paymentTable->Delete(array('customerid'=>$customerid, 'rowid'=>$rowid));
	}
}

$customerid = $_GET['customerid'];

$dataArray = $paymentTable->Select($customerid);

$content = <<<HEREDOC
<form action="addpayment.php?customerid={$customerid}" method="post">
	<input type="submit" class="btn blue add-customer" value="Add Payment"/>
</form>
<form action="requestpaypalpayment.php?customerid={$customerid}" method="post">
	<input type="submit" class="btn orange export" value="Request PayPal Payment"/>
</form>
HEREDOC;

	$content .= <<<HEREDOC
	<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th>Customer ID</th>
		<th>Payment Date</th>
		<th>Payment Amount</th>
		<th>Payment Type</th>
		<th>Payment Note</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
HEREDOC;
	if($dataArray == true){
		foreach($dataArray as $row){
			$content.="
			<tr>
					<td>".$row['customerid']."</td>
					<td>".$region->FormatDate($row['paymentdate'])."</td>
					<td>".$region->FormatCurrency($row['paymentamount'])."</td>
					<td>".$row['paymenttype']."</td>
					<td>".$row['paymentnote']."</td>
					<td>".CreateDeleteButton($row['rowid'], $row['customerid'])."</td>
			</tr>
			";
		}
	}
	$content .= '</tbody>
	<tfoot><tr>
	<td colspan="6"></td>
	</tr></tfoot>
	</table>';
$paymentTable->Disconnect();
?>

<?php echo GetPageHead("List Payments");?>
<div id="body">
<?php echo $content;?>
</div>
<?php echo GetPageFoot();?>