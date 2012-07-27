<?php
class BillingdetailsummaryController extends AppController {
	var $name = 'Billingdetailsummary';
	var $uses = array('Billingbatchmaster', 'Billingbatchdetail','Customerbillingaddressmaster', 'Siteconfiguration');

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
		$this->set('taxdetails', $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid,
					'Billingbatchdetail.lineitemtype' => 40
				)
			)
		));
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
	
	function pdf($billingbatchid, $customerid) {
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
		$this->set('taxdetails', $this->Billingbatchdetail->find('all',
			array(
				'conditions' => array(
					'Billingbatchdetail.billingbatchid' => $billingbatchid,
					'Billingbatchdetail.customerid' => $customerid,
					'Billingbatchdetail.lineitemtype' => 40
				)
			)
		));
		$this->set('billingaddress', $this->Customerbillingaddressmaster->find('first',
			array(
				'conditions' => array(
					'Customerbillingaddressmaster.customerid' => $customerid
				)
			)
		));
		
		$this->set('companyinfo', $this->Siteconfiguration->ListAll());
		$this->set('siteconfiguration', $this->Siteconfiguration->ListAll());
		
		$data = $this->Billingbatchmaster->find('first',
			array(
				'fields' => array('billingdate'),
				'conditions' => array(
					'Billingbatchmaster.billingbatchid' => $billingbatchid
				)
			));
		$billingdate = $data['Billingbatchmaster']['billingdate'];
		$billingdate = str_replace('-','',$billingdate);
		
        // Include Component
        App::import('Component', 'Pdf');
        // Make instance
        $Pdf = new PdfComponent();
        // Invoice name (output name)
        $Pdf->filename = $customerid.'_'.$billingdate; // Without .pdf
        // You can use download or browser here
        $Pdf->output = 'download';
        $Pdf->init();
        // Render the view
        /* Set up new view that won't enter the ClassRegistry */
		$view = new View($this, false);
		$view->viewPath = 'Billingdetailsummary';
		 
		/* Grab output into variable without the view actually outputting! */
		$view_output = $view->render('invoice', '');
		
        $Pdf->process($view_output);
		$this->layout = '';
		$this->render(false);
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