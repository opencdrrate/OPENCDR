<?php
class BillingdetailsummaryController extends AppController {
	var $name = 'Billingdetailsummary';
	var $uses = array('Billingbatchmaster', 'Billingbatchdetail','Customerbillingaddressmaster', 'Contactinformation','Siteconfiguration');
	var $components = array('Email');
	function index($billingbatchid){
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->set('billingdetailsummaries', $this->Billingbatchdetail->find('all',
			array(
				'fields' => array(
								'Billingbatchdetail.billingbatchid',
								'Billingbatchdetail.customerid',
								'SUM(Billingbatchdetail.lineitemamount) AS totalbilled',
								'COUNT(Billingbatchdetail.lineitemamount) AS lineitems',
								'MIN(Billingbatchdetail.periodstartdate) AS periodstartdate',
								'MAX(Billingbatchdetail.periodenddate) AS periodenddate'
								),

				'conditions' => array('Billingbatchdetail.billingbatchid' => $billingbatchid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			)));
		$this->set('batchid',$billingbatchid);
	}
	
	function SendInvoices($billingbatchid){
		$siteconfiguration = $this->Siteconfiguration->ListAll();
		$smtpOptions = array(
						'port' => $siteconfiguration['smtp_port'], 
						'host'=> $siteconfiguration['smtp_host'], 
						'username'=> $siteconfiguration['smtp_user'], 
						'password'=> $siteconfiguration['smtp_password']);
		$from = 'fillmein@please.com';
		$subject = 'Your invoice: payment due';
		$delivery = 'debug';
		$sendAs = 'text';
		

		$customers = $this->Billingbatchdetail->find('all',
			array(
				'fields' => array(
									'Billingbatchdetail.customerid'
								),
				'conditions' => array('Billingbatchdetail.billingbatchid' => $billingbatchid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			));
		foreach($customers as $customer){
			$customerid = $customer['Billingbatchdetail']['customerid'];
			$contactinfo = $this->Contactinformation->find('first', array('conditions'=>array('customerid' => $customerid)));
			$email = $contactinfo['Contactinformation']['primaryemailaddress'];
			
			//Make pdf and add attachment here
			$attachments;
			
			$this->Email->reset();
			$this->Email->delivery = $delivery;
			$this->Email->to = $email;
			$this->Email->from = $from;
			$this->Email->sendAs = $sendAs;
			$this->Email->smtpOptions = $smtpOptions;
			$this->Email->subject = $subject;
			$this->Email->template = 'invoice';
			$this->pdf($billingbatchid, $customerid, 'F');
			$this->Email->attachments = array(
				substr($_SERVER["DOCUMENT_ROOT"],0,-1).$this->webroot.'app/webroot/invoices/'. $customerid . '_' . $billingbatchid . '.pdf'
			);
			
			
			$this->Email->send();
			echo  $this->Email->smtpError . '<br>';	
		}
		$this->layout= '';
	}
	
	function invoice($billingbatchid, $customerid){
		$this->set('customerid', $customerid);
		$this->set('billingdetailsummaries', $this->Billingbatchdetail->find('first',
			array(
				'fields' => array(
								'SUM(Billingbatchdetail.lineitemamount) AS totalbilled',
								'MIN(Billingbatchdetail.periodstartdate) AS periodstartdate',
								'MAX(Billingbatchdetail.periodenddate) AS periodenddate'
								),

				'conditions' => array(
									'Billingbatchdetail.billingbatchid' => $billingbatchid,
									'Billingbatchdetail.customerid' => $customerid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			)));
		$this->set('billingbatchmaster', $this->Billingbatchmaster->find('first',
			array(
				'conditions' => array(
					'Billingbatchmaster.billingbatchid' => $billingbatchid
				)
			)
		));
		
		$this->set('nontaxdetails', $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid,
					'Billingbatchdetail.lineitemtype != 40'
				)
			)
		));
		$this->set('taxdetails', $this->Billingbatchdetail->GetTaxDetails($billingbatchid, $customerid));
		$this->set('billingaddress', $this->Customerbillingaddressmaster->find('first',
			array(
				'conditions' => array(
					'Customerbillingaddressmaster.customerid' => $customerid
				)
			)
		));
		
		$this->set('companyinfo', $this->Siteconfiguration->ListAll());
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		$this->layout = '';
	}
	
	function pdf($billingbatchid, $customerid, $dest = 'D'){
		$view = new View($this);
        $Number = $view->loadHelper('Number');
		$html = $view->loadHelper('Html');
		$filename = $customerid . '_' . $billingbatchid . '.pdf';
		if($dest == 'F'){
			$filename=  'invoices'.DS.$filename;
		}
		else{
		}
		$billingdetailsummaries = $this->Billingbatchdetail->find('first',
			array(
				'fields' => array(
								'SUM(Billingbatchdetail.lineitemamount) AS totalbilled',
								'MIN(Billingbatchdetail.periodstartdate) AS periodstartdate',
								'MAX(Billingbatchdetail.periodenddate) AS periodenddate'
								),

				'conditions' => array(
									'Billingbatchdetail.billingbatchid' => $billingbatchid,
									'Billingbatchdetail.customerid' => $customerid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			));
		$billingbatchmaster =$this->Billingbatchmaster->find('first',
			array(
				'conditions' => array(
					'Billingbatchmaster.billingbatchid' => $billingbatchid
				)
			)
		);
		
		$nontaxdetails = $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid,
					'Billingbatchdetail.lineitemtype != 40'
				)
			)
		);
		$taxdetails = $this->Billingbatchdetail->GetTaxDetails($billingbatchid, $customerid);
		$billingaddress = $this->Customerbillingaddressmaster->find('first',
			array(
				'conditions' => array(
					'Customerbillingaddressmaster.customerid' => $customerid
				)
			)
		);
		
		$companyinfo = $this->Siteconfiguration->ListAll();
		$siteconfiguration = $this->Siteconfiguration->ListAll();
		
        $this->layout = 'pdf'; //this will use the pdf.ctp layout
		
		$currency = $siteconfiguration['currency'];
		$currencySettings = $siteconfiguration['currencysettings'];
		$maxTableHeight = 30;
		$numberOfBlankSpaces = $maxTableHeight - count($nontaxdetails) - count($taxdetails) - 2;

		$companyName = $companyinfo['companyname'];
		$companyAddress1 = $companyinfo['address1'];
		$companyAddress2 = $companyinfo['address2'];
		$companyCity = $companyinfo['city'];
		$companyState = $companyinfo['state'];
		$companyPostal = $companyinfo['postal'];
		$companyCountry = $companyinfo['country'];

		$customerName = $billingaddress['Customer']['customername'];
		$customerAddressLine1 = $billingaddress['Customerbillingaddressmaster']['address1'];
		$customerAddressLine2 = $billingaddress['Customerbillingaddressmaster']['address2'];
		$city = $billingaddress['Customerbillingaddressmaster']['city'];
		$stateorprov = $billingaddress['Customerbillingaddressmaster']['stateorprov'];
		$country = $billingaddress['Customerbillingaddressmaster']['country'];
		$zipcode = $billingaddress['Customerbillingaddressmaster']['zipcode'];
		$customerEmail = '';

		$accountNumber = $customerid;
		$invoiceNumber = $billingbatchmaster['Billingbatchmaster']['billingbatchid'] . '-' . $customerid;
		$startPeriod = $billingdetailsummaries[0]['periodstartdate'];
		$endPeriod = $billingdetailsummaries[0]['periodenddate'];
		$invoiceDate =$billingbatchmaster['Billingbatchmaster']['billingdate'];
		$dueDate=$billingbatchmaster['Billingbatchmaster']['duedate'];

		$serviceperiod = '';
		if(!empty($startPeriod) && !empty($endPeriod)){
			$serviceperiod = $startPeriod . ' to ' . $endPeriod;
		}

		if( empty($customerAddressLine1) && empty($customerAddressLine2) 
			&& empty($city)&& empty($stateorprov)&& empty($zipcode)){
			$customerAddressLine1 = $html->link( 'Edit your customer billing address' ,array( 'controller' => 'Customers', 'action' => 'view', $customerid));
		}

		if(empty($companyName) && empty($companyAddress1) && empty($companyAddress2) && empty($companyCountry) && empty($companyCity)
			&& empty($companyState)&& empty($companyPostal)){
					$companyName = $html->link( 'Setup your company address' ,array( 'controller' => 'Siteconfigurations'));
		}

		$subtotal = 0;
		$nontaxitems = array();
		foreach($nontaxdetails as $item){
			$nontaxitem = array();
			$billingperiod = '';
			if(!empty($item['Billingbatchdetail']["periodstartdate"]) && !empty($item['Billingbatchdetail']["periodenddate"])){
				$billingperiod = 		$item['Billingbatchdetail']["periodstartdate"] 
										. ' to ' 
										. $item['Billingbatchdetail']["periodenddate"];
			}
			
			$nontaxitem[0] = $item['Billingbatchdetail']["periodenddate"];
			$nontaxitem[1] = $item['Billingbatchdetail']["lineitemquantity"];
			$nontaxitem[2] = $billingperiod;
			$nontaxitem[3] = $item['Billingbatchdetail']["lineitemdesc"];
			$nontaxitem[4] = $Number->currency($item['Billingbatchdetail']["lineitemamount"],$currency, $currencySettings);
			$nontaxitems[] = $nontaxitem;
			
			$subtotal += $item['Billingbatchdetail']["lineitemamount"];
		}

		$total = 0;
		$taxitems = array();
		foreach($taxdetails as $item){
			$taxitem = array();
			$taxitem[0] = '';
			$taxitem[1] = '';
			$taxitem[2] = '';
			$taxitem[3] = $item['Billingbatchdetail']["lineitemdesc"];
			$taxitem[4] = $Number->currency($item['Billingbatchdetail']["lineitemamount"],$currency,$currencySettings);
			$taxitems[] = $taxitem;
			
			$total += $item['Billingbatchdetail']["lineitemamount"];
		}
		while(count($taxitems) + count($nontaxitems) < 28){
			$nontaxitems[] = array('','','','','');
		}
		$allitems = array_merge($nontaxitems, $taxitems);
		$total += $subtotal;


		App::import('Vendor', 'FPDF', array('file' => 'fpdf'.DS.'fpdf.php'));
			App::import('Vendor', 'Invoice', array('file' => 'fpdf'.DS.'Invoice.php'));
			
			$fpdf = new Invoice();
			$fpdf->AddPage();
			$fpdf->SetFont('Arial','B',12);
			$fpdf->Cell(40,10,'Invoice');
			$fpdf->Ln();
			$fpdf->SetFont('Arial','B',8);
			$fpdf->Cell(40,10,'Remit To:');
			$fpdf->Ln();	
			$fpdf->SetFont('Arial','',8);
			$fpdf->Image($_SERVER['DOCUMENT_ROOT'] . $this->webroot.'app/webroot/img/'.'company_logo.png',100,20,75);
			$fpdf->Cell(0,5,$companyName);
			$fpdf->Ln();
			$fpdf->Cell(0,5,$companyAddress1);
			$fpdf->Ln();
			$fpdf->Cell(0,5,$companyAddress2);
			$fpdf->Ln();
			$fpdf->Cell(0,5,$companyCity);
			$fpdf->Ln();
			$fpdf->Cell(0,5,$companyPostal);
			$fpdf->Ln();
			$fpdf->Cell(0,5,$companyCountry);
			$fpdf->Ln();
			$fpdf->Cell(160,0,'','T');
			$fpdf->Ln(10);
			
			$fpdf->PrintLineCells(array($customerName, 'Account Number:', $accountNumber));
			$fpdf->PrintLineCells(array($customerAddressLine1, 'Invoice Number:', $invoiceNumber));
			$fpdf->PrintLineCells(array($customerAddressLine2, 'Service Period:', $serviceperiod));
			$fpdf->PrintLineCells(array($city . ', ' . $stateorprov , 'Invoice Date:', $invoiceDate));
			$fpdf->PrintLineCells(array($zipcode, 'Due Date:', $dueDate));
			
			$fpdf->Ln();
			$fpdf->SetCol(0);
			$header = array('Date','Quantity','Billing Period','Description','Amount');

			$fpdf->SetFillColor(100,100,100);
			$fpdf->SetTextColor(0);
			$fpdf->SetDrawColor(0,0,0);
			$fpdf->SetLineWidth(.3);
			$fpdf->SetFont('','B');
			
			$fpdf->table($header, $allitems,$subtotal, $total);
			
			echo $fpdf->Output($filename, $dest);
    }
	
	function allquickbooks($billingbatchid = null){
		$batches = $this->Billingbatchdetail->find('all',
			array(
				'fields' => array(
								'Billingbatchdetail.customerid'
								),

				'conditions' => array('Billingbatchdetail.billingbatchid' => $billingbatchid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			));
		$allData = array();
		foreach($batches as $batch){
			$customerid = $batch['Billingbatchdetail']['customerid'];
			$data = $this->GetQBData($billingbatchid, $customerid);
			$allData[] = $data;
		}
		$this->set('allInvoices', $allData);
		$this->layout = '';
	}
	
	function GetQBData($billingbatchid, $customerid){
		$billingdetailsummaries = $this->Billingbatchdetail->find('first',
			array(
				'fields' => array(
								'SUM(Billingbatchdetail.lineitemamount) AS totalbilled',
								'MIN(Billingbatchdetail.periodstartdate) AS periodstartdate',
								'MAX(Billingbatchdetail.periodenddate) AS periodenddate'
								),

				'conditions' => array(
									'Billingbatchdetail.billingbatchid' => $billingbatchid,
									'Billingbatchdetail.customerid' => $customerid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			));
		$billingbatchmaster = $this->Billingbatchmaster->find('first',
			array(
				'conditions' => array(
					'Billingbatchmaster.billingbatchid' => $billingbatchid
				)
			)
		);
		$Billingbatchdetails = $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid
				)
			)
		);
		
		$billingaddress = $this->Customerbillingaddressmaster->find('first',
			array(
				'conditions' => array(
					'Customerbillingaddressmaster.customerid' => $customerid
				)
			)
		);
		$data = array();
		$data['Account'] = 'Accounts Receivable';
		$data['PaymentID'] = '001';
		$data['DocumentNumber'] = '';
		
		$data['StatementDate'] = $billingbatchmaster['Billingbatchmaster']['duedate'];
		$data['CustomerName'] = $billingaddress['Customer']['customername'];
		$data['StatementTotal'] = $billingdetailsummaries[0]['totalbilled'];
		$data['CustomerAddress1'] = $billingaddress['Customerbillingaddressmaster']['address1'];
		$data['CustomerAddress2'] = $billingaddress['Customerbillingaddressmaster']['address2'];
		$data['CustomerCity'] = $billingaddress['Customerbillingaddressmaster']['city'];
		$data['CustomerState'] = $billingaddress['Customerbillingaddressmaster']['stateorprov'];
		$data['CustomerCountry'] = $billingaddress['Customerbillingaddressmaster']['country'];
		$data['CustomerZipCode'] = $billingaddress['Customerbillingaddressmaster']['zipcode'];
		$data['DueDate'] = '';
		$data['ShipDate'] = '';
		$data['CustomerPaymentTerms'] = '';
		
		
		$details = array();
		$i = 2;
		foreach($Billingbatchdetails as $item){
			$detail = array();
			$detail['LineID'] = $i;
			$detail['ItemName'] = $item['Billingbatchdetail']['lineitemdesc'];
			$detail['ItemDescription'] = $item['Billingbatchdetail']['lineitemdesc'];
			$detail['Amount'] = $item['Billingbatchdetail']['lineitemamount'];
			$detail['Quantity'] = $item['Billingbatchdetail']['lineitemquantity'];
			$detail['StatementDate'] = $item['Billingbatchdetail']['periodenddate'];
			$detail['AccountDescription'] = 'Income Account';
			$detail['DocumentNumber'] = '';
			$detail['EntryDateTime'] = '';
			$detail['Price'] = '';
			if($item['Billingbatchdetail']['lineitemtype'] == '40'){
				$detail['Taxable'] = 'N';
			}
			else{
				$detail['Taxable'] = 'Y';
			}
			$i++;
			$details[] = $detail;
		}
		$data['Details'] = $details;
		return $data;
	}
	
	function quickbooks($billingbatchid, $customerid){
		$billingdetailsummaries = $this->Billingbatchdetail->find('first',
			array(
				'fields' => array(
								'SUM(Billingbatchdetail.lineitemamount) AS totalbilled',
								'MIN(Billingbatchdetail.periodstartdate) AS periodstartdate',
								'MAX(Billingbatchdetail.periodenddate) AS periodenddate'
								),

				'conditions' => array(
									'Billingbatchdetail.billingbatchid' => $billingbatchid,
									'Billingbatchdetail.customerid' => $customerid),
				'group' => array('Billingbatchdetail.customerid', 'Billingbatchdetail.billingbatchid')
			));
		$billingbatchmaster = $this->Billingbatchmaster->find('first',
			array(
				'conditions' => array(
					'Billingbatchmaster.billingbatchid' => $billingbatchid
				)
			)
		);
		$Billingbatchdetails = $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid
				)
			)
		);
		
		$billingaddress = $this->Customerbillingaddressmaster->find('first',
			array(
				'conditions' => array(
					'Customerbillingaddressmaster.customerid' => $customerid
				)
			)
		);
		$this->set('Account', 'Accounts Receivable');
		$this->set('PaymentID', '001');
		$this->set('DocumentNumber', '');
		
		$this->set('StatementDate', $billingbatchmaster['Billingbatchmaster']['duedate']);
		$this->set('CustomerName', $billingaddress['Customer']['customername']);
		$this->set('StatementTotal', $billingdetailsummaries[0]['totalbilled']);
		$this->set('CustomerAddress1', $billingaddress['Customerbillingaddressmaster']['address1']);
		$this->set('CustomerAddress2', $billingaddress['Customerbillingaddressmaster']['address2']);
		$this->set('CustomerCity', $billingaddress['Customerbillingaddressmaster']['city']);
		$this->set('CustomerState', $billingaddress['Customerbillingaddressmaster']['stateorprov']);
		$this->set('CustomerCountry', $billingaddress['Customerbillingaddressmaster']['country']);
		$this->set('CustomerZipCode', $billingaddress['Customerbillingaddressmaster']['zipcode']);
		$this->set('DueDate', '');
		$this->set('ShipDate', '');
		$this->set('CustomerPaymentTerms', '');
		
		$details = array();
		$i = 2;
		foreach($Billingbatchdetails as $item){
			$detail = array();
			$detail['LineID'] = $i;
			$detail['ItemName'] = $item['Billingbatchdetail']['lineitemdesc'];
			$detail['ItemDescription'] = $item['Billingbatchdetail']['lineitemdesc'];
			$detail['Amount'] = $item['Billingbatchdetail']['lineitemamount'];
			$detail['Quantity'] = $item['Billingbatchdetail']['lineitemquantity'];
			$detail['StatementDate'] = $item['Billingbatchdetail']['periodenddate'];
			$detail['AccountDescription'] = 'Income Account';
			$detail['DocumentNumber'] = '';
			$detail['EntryDateTime'] = '';
			$detail['Price'] = '';
			if($item['Billingbatchdetail']['lineitemtype'] == '40'){
				$detail['Taxable'] = 'N';
			}
			else{
				$detail['Taxable'] = 'Y';
			}
			$i++;
			$details[] = $detail;
		}
		$this->set('details', $details);
		$this->set('customerid', $customerid);
		
		$this->layout = '';
	}

}
?>