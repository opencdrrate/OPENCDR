<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="invoice.iif"');

foreach($allInvoices as $invoice){
$header = <<< HEREDOC
!TRNS	TRNSID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	TOPRINT	ADDR1	ADDR2	ADDR3	ADDR4	ADDR5	DUEDATE	TERMS	PAID	SHIPDATE
!SPL	SPLID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	QNTY	PRICE	INVITEM	PAYMETH	TAXABLE	REIMBEXP	EXTRA
!ENDTRNS
TRNS	{$invoice['PaymentID']}	INVOICE	{$invoice['StatementDate']}	{$invoice['Account']}	{$invoice['CustomerName']}		{$invoice['StatementTotal']}	{$invoice['DocumentNumber']}		N	N	{$invoice['CustomerAddress1']}	{$invoice['CustomerAddress2']}	{$invoice['CustomerCity']}	{$invoice['CustomerState']}, {$invoice['CustomerCountry']}	{$invoice['CustomerZipCode']}	{$invoice['DueDate']}	{$invoice['CustomerPaymentTerms']}	N	{$invoice['ShipDate']}

HEREDOC;
	echo $header;
	foreach($invoice['Details'] as $detail){
echo <<< HEREDOC
SPL	{$detail['LineID']}	INVOICE	{$detail['StatementDate']}	{$detail['AccountDescription']}			-{$detail['Amount']}	{$detail['DocumentNumber']}	{$detail['ItemDescription']}	N	-{$detail['Quantity']}	{$detail['Price']}	{$detail['ItemName']}		{$detail['Taxable']}	N	NOTHING

HEREDOC;
}
echo <<< HEREDOC
ENDTRNS

HEREDOC;
}
?>