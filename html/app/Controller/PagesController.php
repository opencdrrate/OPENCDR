<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array('CallrecordmasterHeld',
					'UncategorizedCdr',
					'CallrecordmasterTbr',
					'Callrecordmaster',
					'Customer',
					'Customertaxsetup',
					'Didmaster',
					'Ipaddressmaster',
					'Npamaster',
					'Billingbatchmaster',
					'Processhistory',
					'Tieredoriginationratecentermaster',
					'Siteconfiguration');

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @access public
 */
	function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$RatingErrorCount = $this->CallrecordmasterHeld->find('count');
		$UncategorizedCdrCount = $this->UncategorizedCdr->find('count', array('conditions' => array('calltype IS NULL')));
		$RatingQueueCount = $this->CallrecordmasterTbr->find('count', array('conditions' => array('calltype IS NOT NULL')));
		$CRMCount = $this->Callrecordmaster->find('count');
		$CustomerCount = $this->Customer->find('count', array('recursive'=>-1));
		$CustomertaxsetupCount = $this->Customertaxsetup->find('count');
		$DidCount = $this->Didmaster->find('count');
		$IpaddressCount = $this->Ipaddressmaster->find('count');
		$NpaCount = $this->Npamaster->find('count');
		$BillingbatchCount = $this->Billingbatchmaster->find('count');
		$ProcesshistoryCount = $this->Processhistory->find('count');
		$RateCenterCount = $this->Tieredoriginationratecentermaster->find('count');
		$settings = $this->Siteconfiguration->ListAll();
		$sitename = '';
		if(isset($settings['sitename'])){
			$sitename = $settings['sitename'];
		}
		$this->set('sitename', $sitename);
		$this->set('RatingErrorCount', $RatingErrorCount);
		$this->set('UncategorizedCdrCount', $UncategorizedCdrCount);
		$this->set('RatingQueueCount', $RatingQueueCount);
		$this->set('CRMCount', $CRMCount);
		$this->set('CustomerCount', $CustomerCount);
		$this->set('CustomertaxsetupCount', $CustomertaxsetupCount);
		$this->set('DidCount', $DidCount);
		$this->set('IpaddressCount', $IpaddressCount);
		$this->set('NpaCount', $NpaCount);
		$this->set('BillingbatchCount', $BillingbatchCount);
		$this->set('ProcesshistoryCount', $ProcesshistoryCount);
		$this->set('RateCenterCount', $RateCenterCount);
		
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}
}
