<?php
include 'lib/SQLQueryFuncs.php';
include 'config.php';
include 'lib/Page.php';
include 'lib/IIFFile.php';
$batchid = $_GET["batchid"];

function GetInvoiceIIF($billingbatchid, $customerid){
	include 'config.php';
	#build IIF file and redirect to it
	$iifFile = new iifFile();
	
	#foreach customer : create a new transaction
	/*
		Transaction missing fields :
		$CustomerPaymentTerms;	
		public $DueDate = '';
		public $ShipDate = '';
	*/
	
	$transaction = new Transaction();
	$db = pg_connect($connectstring);
	
	#Hardcoded or missing values :
	$transaction->Account = "Accounts Receivable";
	$transaction->PaymentID = "001";
	
	#Customer Master : 
	$customerQuery = "SELECT \"customername\" 
						FROM customermaster 
						WHERE \"customerid\" = '".$customerid."';";
	$customerQueryResult = pg_query($customerQuery);
	$customerData = pg_fetch_assoc($customerQueryResult);
	
	$transaction->CustomerName = $customerData['customername'];
	
	#customerbillingaddressmaster:
	$customerAddressQuery = "SELECT address1, address2, city, stateorprov, zipcode, country
		FROM customerbillingaddressmaster WHERE customerid = '".$customerid."';";
	$customerAddressQueryResult = pg_query($customerAddressQuery);
	$addressData = pg_fetch_assoc($customerAddressQueryResult);
	
	$transaction->CustomerAddress1 = $addressData['address1'];
	$transaction->CustomerAddress2 = $addressData['address2'];
	$transaction->CustomerCity = $addressData['city'];
	$transaction->CustomerState = $addressData['stateorprov'];
	$transaction->CustomerZipCode = $addressData['zipcode']; 
	$transaction->CustomerCountry = $addressData['country'];
	
	#period data from billingbatchdetails
	$periodQuery ='SELECT
						sum(lineitemamount) as "amount"
						FROM billingbatchdetails 
						WHERE billingbatchid = \''.$billingbatchid.'\' 
						AND customerid = \''. $customerid .'\' 
						group by customerid order by customerid;';
	$periodQueryResult = pg_query($periodQuery);
	$periodData = pg_fetch_assoc($periodQueryResult);
	$transaction->StatementTotal = $periodData['amount'];
	
	#billingbatchmaster
	$batchMasterQuery = "SELECT billingbatchid, billingdate, duedate, billingcycleid, usageperiodend
			FROM billingbatchmaster WHERE billingbatchid = '".$billingbatchid."';";
	$batchMasterQueryResult = pg_query($batchMasterQuery);
	$batchMasterData = pg_fetch_assoc($batchMasterQueryResult);
	$transaction->EntryDateTime = $batchMasterData['billingdate'];
	$transaction->StatementDate = $batchMasterData['duedate'];	
	
	#line items from billingbatchdetails
	$allItems = array();
	$lineItemQuery = "SELECT lineitemdesc as \"description\", 
			lineitemamount as \"amount\",
			lineitemquantity as \"quantity\",
			periodstartdate,
			periodenddate,
			lineitemtype
			FROM billingbatchdetails
			WHERE customerid = '".$customerid."'
			AND billingbatchid = '".$billingbatchid."';";
			
	$lineItemQueryResults = pg_query($lineItemQuery);
	$lineID = 2;
	while($lineItem = pg_fetch_assoc($lineItemQueryResults)){
		$lineID++;
	/*	
	missing fields for details :
		public $EntryDateTime = '';
		public $DocumentNumber = '';
		public $Price = '';
	*/
		$detail = new Detail();
		$detail->LineID = $lineID;
		$detail->ItemName = $lineItem['description'];		#possibly wrong?
		$detail->ItemDescription=$lineItem['description'];
		$detail->Amount = $lineItem['amount'];
		$detail->Quantity = $lineItem['quantity'];
		$detail->StatementDate = $lineItem['periodenddate'];
		$detail->AccountDescription = 'Income Account';
		if($lineItem['lineitemtype'] == '40'){
			$detail->Taxable = 'N';
		}
		else{
			$detail->Taxable = 'Y';
		}
		/*
		$item = array("Date" => $lineItem['periodenddate']
					,"Quantity" => $lineItem['quantity']
					,"Billing Period" => $lineItem['periodstartdate'] ." to ". $lineItem['periodenddate']
				);
		*/
		$transaction->AddDetail($detail);
	}
	$iifFile->AddTransaction($transaction);
	#make the file and redirect
	
	$iifContent = $iifFile->ToString();
	
	return $iifContent;
}
if(isset($_GET['qb'])){
	$customerid = $_GET["customerid"];
	
	$iifContent = GetInvoiceIIF($batchid, $customerid);
	$file = fopen("files/invoice.iif", 'w'); 
	fwrite($file, $iifContent);
	fclose($file);
	header('location: '. "files/invoice.iif");
}
if(isset($_GET['qball'])){
	$db = pg_connect($connectstring);
	
	$query = 'SELECT customerid
			FROM billingbatchdetails WHERE billingbatchid = \''.$batchid.'\' group by customerid order by customerid;';
	
	$result = pg_query($query);
	$file = fopen("files/invoice.iif", 'w'); 
	while($myrow = pg_fetch_assoc($result)) {
		$customerid = $myrow['customerid'];
		$iifContent = GetInvoiceIIF($batchid, $customerid);
		fwrite($file, $iifContent);
	}
	fclose($file);
	header('location: '. "files/invoice.iif");
}

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
<th>Total Billed Amount</th>
<th>Line Items</th>
<th>Start Date</th>
<th>End Date</th>
<th>Print Invoice</th>
<th>Export to Quickbooks</th>
<th>View/Edit</th>
</tr>
</thead>
<tbody>
HEREDOC;

	$db = pg_connect($connectstring);

	#$tempstring = "printinvoice.php?batchid=".$batchid."&customerid=";
	$query = 'SELECT customerid, sum(lineitemamount) as "amount",
			count(*) as "items", max(periodstartdate) as "periodstartdate", max(periodenddate) as "periodenddate"
			FROM billingbatchdetails WHERE billingbatchid = \''.$batchid.'\' group by customerid order by customerid;';


	$result = pg_query($query); 
        if (!$result) { 
            echo "Problem with query " . $query . "<br/>"; 
            echo pg_last_error(); 
            exit(); 
        } 

	while($myrow = pg_fetch_assoc($result)) {

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['amount']}</td>
<td>{$myrow['items']}</td>
<td>{$myrow['periodstartdate']}</td>
<td>{$myrow['periodenddate']}</td>
<td class="actions" align="center">
<a href=printinvoice.php?batchid={$batchid}&customerid={$myrow['customerid']} class="italic payment">Invoice</a>
</td><td class="actions" align="center">
<a href=viewbillbatchdetails.php?batchid={$batchid}&customerid={$myrow['customerid']}&qb=y class="italic payment">Quickbooks</a>
</td><td class="actions" align="center">
<a href=viewlineitems.php?batchid={$batchid}&customerid={$myrow['customerid']}&qb=y class="italic payment">View/Edit Details</a>
</td></tr>
HEREDOC;

	}

	$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="8"></td>
	    	</tr>
	    </tfoot>
		</table>';

		

	#$queryResult = SQLSelectQuery($connectstring, $query, ",", "\n");
	#$table = QueryResultToTable($queryResult, ',', array("CustomerID","Amount","Items","Start Date","End Date", "Print Invoice"));
?>
<?php echo GetPageHead("Billing Batch Details", "listbillbatches.php");?>
	
<div id="body">

	<form name="export" action="viewbillbatchdetails.php" method="get">
   	<input type="submit" class="btn orange export" value="Export all to Quickbooks">
		<input type="hidden" value="y" name="qball">
		<input type="hidden" value="<?php echo htmlspecialchars($batchid);?>" name="batchid">
	</form>

	<?php echo $htmltable; ?>
</div>
<?php echo GetPageFoot();?>
