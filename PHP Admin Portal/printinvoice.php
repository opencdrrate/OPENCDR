<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/localizer.php';
	
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	$locale = $manager->GetSetting('region');
	$companyName = $manager->GetSetting('company_name');
	$companyAddress1 = $manager->GetSetting('company_address1');
	$companyAddress2 = $manager->GetSetting('company_address2');
	$companyCountry = $manager->GetSetting('company_country');
	$logopath = $manager->GetSetting('logo');
	
	$region = new localizer($locale);
	
	#page config
	$maxTableHeight = 30;
	$pageWidth = 900;
	
	#user input
	$customerid = $_GET["customerid"];
	$billingbatchid = $_GET["batchid"];
	
	$accountNumber = $customerid;
	$invoiceNumber = $billingbatchid.'-'.$customerid;
	
	
	$db = pg_connect($connectstring);
	#Customer Master : 
	$customerQuery = "SELECT \"customername\" 
						FROM customermaster 
						WHERE \"customerid\" = '".$customerid."';";
	$customerQueryResult = pg_query($customerQuery);
	$customerData = pg_fetch_assoc($customerQueryResult);
	
	$customerName = $customerData['customername'];
	
	#customerbillingaddressmaster:
	$customerAddressQuery = "SELECT address1, address2, city, stateorprov, zipcode
		FROM customerbillingaddressmaster WHERE customerid = '".$customerid."';";
	$customerAddressQueryResult = pg_query($customerAddressQuery);
	$addressData = pg_fetch_assoc($customerAddressQueryResult);
	
	$customerAddressLine1 = $addressData['address1'];
	$customerAddressLine2 = $addressData['address2'];
	$customerAddressLine3 = $addressData['city'] . ", " . $addressData['stateorprov'] . ", " . $addressData['zipcode']; 
	$customerEmail = "";
	
	#period data from billingbatchdetails
	$periodQuery ='SELECT customerid, 
						min(periodstartdate) as "periodstartdate", 
						max(periodenddate) as "periodenddate"
						FROM billingbatchdetails 
						WHERE billingbatchid = \''.$billingbatchid.'\' 
						AND customerid = \''. $customerid .'\' 
						group by customerid order by customerid;';
	$periodQueryResult = pg_query($periodQuery);
	$periodData = pg_fetch_assoc($periodQueryResult);
	$startPeriod = $region->FormatDate($periodData['periodstartdate']);
	$endPeriod = $region->FormatDate($periodData['periodenddate']);
	$servicePeriod = $startPeriod . ' to ' . $endPeriod;
	
	#billingbatchmaster
	$batchMasterQuery = "SELECT billingbatchid, billingdate, duedate, billingcycleid, usageperiodend
			FROM billingbatchmaster WHERE billingbatchid = '".$billingbatchid."';";
	$batchMasterQueryResult = pg_query($batchMasterQuery);
	$batchMasterData = pg_fetch_assoc($batchMasterQueryResult);
	$invoiceDate = $region->FormatDate($batchMasterData['billingdate']);
	$dueDate = $region->FormatDate($batchMasterData['duedate']);	
	
	#line items from billingbatchdetails
	$allItems = array();
	$lineItemQuery = "SELECT lineitemdesc as \"description\", 
			lineitemamount as \"amount\",
			lineitemquantity as \"quantity\",
			periodstartdate,
			periodenddate
			FROM billingbatchdetails
			WHERE customerid = '".$customerid."'
			AND billingbatchid = '".$billingbatchid."'"
			." AND lineitemtype != '40';";
	$lineItemQueryResults = pg_query($lineItemQuery);
	while($lineItem = pg_fetch_assoc($lineItemQueryResults)){
		$item = array("Date" => $lineItem['periodenddate']
					,"Quantity" => $lineItem['quantity']
					,"Billing Period" => $region->FormatDate($lineItem['periodstartdate']) 
					." to ". $region->FormatDate($lineItem['periodenddate'])
					,"Description" => $lineItem['description']
					,"Amount" => $lineItem['amount']
				);
		$allItems[] = $item;
	}
	
	$taxItems = array();
	$taxItemQuery = "SELECT lineitemdesc as \"description\", 
			lineitemamount as \"amount\"
			FROM billingbatchdetails
			WHERE customerid = '".$customerid."'
			AND billingbatchid = '".$billingbatchid."'"
			." AND lineitemtype = '40';";
	$taxItemQueryResults = pg_query($taxItemQuery);
	while($lineItem = pg_fetch_assoc($taxItemQueryResults)){
		$item = array("Description" => $lineItem['description']
					,"Amount" => $lineItem['amount']
				);
		$taxItems[] = $item;
		
	}
	
	$numberOfBlankSpaces = $maxTableHeight - count($allItems) - count($taxItems) - 2;
	$subtotal = 0; #calculate me
	$total = 0; #calculate me
?>

<html>
<head>
<style type="text/css">
body{font-family:Arial, Gadget, sans-serif}
table,td,th{border: 1px solid black;}
table{ border-collapse: collapse;}
.noborder {border: 0px solid white;}
th {background-color:#999999; align:center}
tr.top td { border-bottom-width: 0px; }
tr.middle td{ border-top-width: 0px;
				border-bottom-width: 0px;}
tr.bottom td { border-top-width: 0px; }

</style>
</head>
<body>

<table class="noborder" width=<?php echo $pageWidth;?>>
<tr>
<td width="45%" class="noborder">
<h2>Invoice</h2><p>
<b>Remit To: </b><br>
<?php echo $companyName;?><br>
<?php echo $companyAddress1;?><br>
<?php echo $companyAddress2;?><br>
<?php echo $companyCountry;?><br>
</td>
<td class="noborder">
<img src=<?php echo $logopath;?>>
</td>
</tr>
</table>

<hr color="#333333">
<br>
<br>
<table width=<?php echo $pageWidth;?> class="noborder">
<tr>
<td width="45%" class="noborder" valign="top">
<?php echo $customerName;?><br>
<?php echo $customerAddressLine1;?><br>
<?php echo $customerAddressLine2;?><br>
<?php echo $customerAddressLine3;?><br>
<?php echo $customerEmail;?>
</td>

<td class="noborder" valign="top">
Account Number:<br>
Invoice Number:<br>
Service Period<br>
Invoice Date<br>
Due Date:</td>

<td class="noborder" align="right" valign="top">
<?php echo $accountNumber;?><br>
<?php echo $invoiceNumber;?><br>
<?php echo $servicePeriod;?><br>
<?php echo $invoiceDate;?><br>
<?php echo $dueDate;?></td>

</tr>
</table>
<table width=<?php echo $pageWidth;?>>
<tr>
	<th>Date</th><th>Quantity</th><th>Billing Period</th><th>Description</th><th>Amount</th>
</tr>
<?php 
foreach($allItems as $item){
	echo '<tr class="middle">';
	echo '<td align="center">'. $item["Date"] .'</td>';
	echo '<td align="center">'. $item["Quantity"] .'</td>';
	echo '<td align="center">'. $item["Billing Period"] .'</td>';
	echo '<td>'. $item["Description"] .'</td>';
	echo '<td align="right">'. $region->FormatCurrency($item["Amount"]).'</td>';
	echo '</tr>';
	
	$subtotal += $item["Amount"];
}
?>
<?php
for($i = 0 ; $i < $numberOfBlankSpaces ; $i++){
	echo '<tr class="middle">
	<td><br></td><td></td><td></td><td></td><td></td>
	</tr>';
}
?>
<tr class="middle">
	<td></td><td></td><td></td><td>Subtotal</td><td align="right"><?php echo $region->FormatCurrency($subtotal);?></td>
</tr>
<?php 
foreach($taxItems as $item){
	echo '<tr class="middle">';
	echo '<td></td>';
	echo '<td align="center"></td>';
	echo '<td></td>';
	echo '<td>'. $item["Description"] .'</td>';
	echo '<td align="right">'. '$'.sprintf("%01.2f",$item["Amount"]).'</td>';
	echo '</tr>';
	$total += $item["Amount"];
}
$total += $subtotal;
?>
<tr class="top">
	<td></td><td></td><td></td><td>Total</td><td align="right"><?php echo $region->FormatCurrency($total);?></td>
</tr>
</table>

</body>
</html>