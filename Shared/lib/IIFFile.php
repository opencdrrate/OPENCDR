<?php

class IIFFile{
	private $Transactions = array();
	
	function AddTransaction($transaction){
		$this->Transactions[] = $transaction;
	}
	
	function ToString(){
		$output = '';
		foreach($this->Transactions as $transaction){
			$output .= $transaction->ToString();
		}
		return $output;
	}
}

class Transaction{
	private $Details = array();
	public $PaymentID = '';
	public $StatementDate = '';
	public $DueDate = '';
	public $ShipDate = '';
	public $CustomerName = '';
	public $StatementTotal = '';
	public $DocumentNumber = '';	
	public $CustomerAddress1 = '';	
	public $CustomerAddress2 = '';	
	public $CustomerCity = '';	
	public $CustomerState = ''; 
	public $CustomerCountry = '';	
	public $CustomerZipCode = '';	
	public $CustomerPaymentTerms = '';
	public $Account = '';
	
	function AddDetail($detail){
		$this->Details[] = $detail;
	}
	function ToString(){
/*
!TRNS	TRNSID	TRNSTYPE	DATE		ACCNT				NAME		CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	TOPRINT	ADDR1		ADDR2	ADDR3	ADDR4	ADDR5	DUEDATE		TERMS	PAID	SHIPDATE
TRNS	53		INVOICE		7/16/1998	Accounts Receivable	Customer	""		20		1		""		N		N		Customer	""		""		""		""		7/16/1998	""		N		7/16/1998
*/
		$header = <<< HEREDOC
TRNS	{$this->PaymentID}	INVOICE	{$this->StatementDate}	{$this->Account}	{$this->CustomerName}		{$this->StatementTotal}	{$this->DocumentNumber}		N	N	{$this->CustomerAddress1}	{$this->CustomerAddress2}	{$this->CustomerCity}	{$this->CustomerState}, {$this->CustomerCountry}	{$this->CustomerZipCode}	{$this->DueDate}	{$this->CustomerPaymentTerms}	N	{$this->ShipDate}

HEREDOC;
		$startTransactionToken = <<< HEREDOC
!TRNS	TRNSID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	TOPRINT	ADDR1	ADDR2	ADDR3	ADDR4	ADDR5	DUEDATE	TERMS	PAID	SHIPDATE
!SPL	SPLID	TRNSTYPE	DATE	ACCNT	NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	QNTY	PRICE	INVITEM	PAYMETH	TAXABLE	REIMBEXP	EXTRA
!ENDTRNS

HEREDOC;
		$endTransToken = <<< HEREDOC
ENDTRNS

HEREDOC;
		$output = '';
		$output .= $startTransactionToken;
		$output .= $header;
		#print each detail here
		foreach($this->Details as $detail){
			$output .= $detail->ToString();
		}
		$output .= $endTransToken;	
		return $output;
	}
}

class Detail{
	public $LineID = '';
	public $EntryDateTime = '';
	public $StatementDate = '';
	public $AccountDescription = '';
	public $Quantity = '';
	public $Price = '';
	public $Amount = '';
	public $DocumentNumber = '';
	public $ItemDescription = '';
	public $ItemName = '';
	public $Taxable = '';
/*

!SPL	SPLID	TRNSTYPE	DATE		ACCNT			NAME	CLASS	AMOUNT	DOCNUM	MEMO	CLEAR	QNTY	PRICE	INVITEM		PAYMETH	TAXABLE		REIMBEXP	EXTRA
SPL		54		INVOICE		7/16/1998	Income Account	""		""		-20		""		""		N		-2		10		Sales Item	""		N			N			NOTHING
*/
	function ToString(){
		$detail = <<< HEREDOC
SPL	{$this->LineID}	INVOICE	{$this->StatementDate}	{$this->AccountDescription}			-{$this->Amount}	{$this->DocumentNumber}	{$this->ItemDescription}	N	-{$this->Quantity}	{$this->Price}	{$this->ItemName}		{$this->Taxable}	N	NOTHING

HEREDOC;
		return $detail;
	}
}
?>