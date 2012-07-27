<?php 
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="invoice.iif"');
$header = <<< HEREDOC
TRNS	{$PaymentID}	INVOICE	{$StatementDate}	{$Account}	{$CustomerName}		{$StatementTotal}	{$DocumentNumber}		N	N	{$CustomerAddress1}	{$CustomerAddress2}	{$CustomerCity}	{$CustomerState}, {$CustomerCountry}	{$CustomerZipCode}	{$DueDate}	{$CustomerPaymentTerms}	N	{$ShipDate}

HEREDOC;
?>
!TRNS	TRNSID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	TOPRINT	ADDR1	ADDR2	ADDR3	ADDR4	ADDR5	DUEDATE	TERMS	PAID	SHIPDATE
!SPL	SPLID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	QNTY	PRICE	INVITEM	PAYMETH	TAXABLE	REIMBEXP	EXTRA
!ENDTRNS
<?php echo $header?>
<?php foreach($details as $detail){
echo <<< HEREDOC
SPL	{$detail['LineID']}	INVOICE	{$detail['StatementDate']}	{$detail['AccountDescription']}			-{$detail['Amount']}	{$detail['DocumentNumber']}	{$detail['ItemDescription']}	N	-{$detail['Quantity']}	{$detail['Price']}	{$detail['ItemName']}		{$detail['Taxable']}	N	NOTHING

HEREDOC;
}?>
ENDTRNS