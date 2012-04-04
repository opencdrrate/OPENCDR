<?php
include 'lib/Page.php';
function CreateDeleteButton($rowid, $customerid){
	$deleteButton = '<form action="listpayments.php?customerid='.$customerid.'" method="post">';
	$deleteButton .=	'<input type="hidden" name="function" value="delete"/>';
	$deleteButton .=	'<input type="hidden" name="customerid" value="'.$customerid.'" />';
	$deleteButton .=	'<input type="hidden" name="rowid" value="'.$rowid.'"/>';
	$deleteButton .= 	'<input type="submit" class="btn-action delete" value="delete">';
	$deleteButton .= '</form>';
	return $deleteButton;
}

include 'config.php';

if(isset($_POST['function'])){
	$function = $_POST['function'];
	if($function == "delete"){
		$customerid = $_POST['customerid'];
		$rowid = $_POST['rowid'];
		$connect = pg_connect($connectstring);
		$deleteStatement = "DELETE FROM paymentmaster WHERE customerid = '"
				.$customerid."' AND rowid = '".$rowid."';";
		pg_query($deleteStatement);
		pg_close($connect);
		echo 'row deleted<br>';
	}
}

$customerid = $_GET['customerid'];
$queryString = 'SELECT customerid, paymentdate, paymentamount, paymenttype, paymentnote, rowid
FROM paymentmaster WHERE customerid = \''.$customerid.'\';';
$db = pg_connect($connectstring);
$queryResults = pg_query($queryString);
$dataArray = pg_fetch_all($queryResults);

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
	</tr>
	</thead>
	<tbody>
HEREDOC;
	if($dataArray == true){
		foreach($dataArray as $row){
			$content.="
			<tr>
					<td>".$row['customerid']."</td>
					<td>".$row['paymentdate']."</td>
					<td>".$row['paymentamount']."</td>
					<td>".$row['paymenttype']."</td>
					<td>".$row['paymentnote']."</td>
					<td>".CreateDeleteButton($row['rowid'], $row['customerid'])."</td>
			</tr>
			";
		}
	}
	$content .= '</tbody>
	</table>';
pg_close($db);
?>

<?php echo GetPageHead("List Payments");?>
<div id="body">
<?php echo $content;?>
</div>
<?php echo GetPageFoot();?>