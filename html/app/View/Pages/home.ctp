<h2><?php echo $sitename?></h2>
<table>
<tr><td>Getting Started Guide:	</td><td><?php
		echo $this->Html->link('View', 'https://sourceforge.net/p/opencdrrate/home/Typical%20Workflow/', array('target' => '_blank'));
?></td></tr>
<tr><td>Uncategorized CDR:	</td><td><?php
		echo $this->Html->link($this->Number->currency($UncategorizedCdrCount,'',array('places'=>0)),array( 'controller' => 'UncategorizedCdrs'));
?></td></tr>
<tr><td>Rating Errors : 	</td><td><?php
		echo $this->Html->link($this->Number->currency($RatingErrorCount,'',array('places'=>0)),array( 'controller' => 'RatingErrors'));
?></td></tr>
<tr><td>Rating Queue:	</td><td><?php
		echo $this->Html->link($this->Number->currency($RatingQueueCount,'',array('places'=>0)),array( 'controller' => 'RatingQueue'));
?></td></tr>
<tr><td>Rated Calls:	</td><td><?php
		echo $this->Html->link($this->Number->currency($CRMCount,'',array('places'=>0)),array( 'controller' => 'RatedCalls'));
?></td></tr>
<tr><td>Customers:	</td><td><?php
		echo $this->Html->link($this->Number->currency($CustomerCount,'',array('places'=>0)),array( 'controller' => 'Customers'));
?></td></tr>
<tr><td>Customer Rates (Wholesale):	</td><td><?php
		echo $this->Html->link('Edit',array( 'controller' => 'Wholesalerates'));
?></td></tr>
<tr><td>Customer Plans (Retail): </td><td><?php
		echo $this->Html->link('Edit',array( 'controller' => 'Retailplans'));
?></td></tr>
<tr><td>Customer Tax Setup:	</td><td><?php
		echo $this->Html->link($this->Number->currency($CustomertaxsetupCount,'',array('places'=>0)),array( 'controller' => 'Customertaxsetups'));
?></td></tr>
<tr><td>DIDs:	</td><td><?php
		echo $this->Html->link($this->Number->currency($DidCount,'',array('places'=>0)),array( 'controller' => 'Dids'));
?></td></tr>
<tr><td>IP Addresses:	</td><td><?php
		echo $this->Html->link($this->Number->currency($IpaddressCount,'',array('places'=>0)),array( 'controller' => 'Ipaddresses'));
?></td></tr>
<tr><td>NPA Information:	</td><td><?php
		echo $this->Html->link($this->Number->currency($NpaCount,'',array('places'=>0)),array( 'controller' => 'Npas'));
?></td></tr>
<tr><td>Billing Batches:	</td><td><?php
		echo $this->Html->link($this->Number->currency($BillingbatchCount,'',array('places'=>0)),array( 'controller' => 'Billingbatches'));
?></td></tr>
<tr><td>Process History:	</td><td><?php
		echo $this->Html->link($this->Number->currency($ProcesshistoryCount,'',array('places'=>0)),array( 'controller' => 'Processhistories'));
?></td></tr>
<tr><td>Rate Centers:	</td><td><?php
		echo $this->Html->link($this->Number->currency($RateCenterCount,'',array('places'=>0)),array( 'controller' => 'RateCenters'));
?></td></tr>
<tr><td>Reports:	</td><td><?php
		echo $this->Html->link('17','/pages/reports');
?></td></tr>
<tr><td>Users:	</td><td><?php
		echo $this->Html->link('View',array('controller' => 'Users'));
?></td></tr>
<tr><td>Adhoc Query:	</td><td><?php
		echo $this->Html->link('Query',array('controller' => 'AdHocQuery'));
?></td></tr>
<tr><td>System Configuration:	</td><td><?php
		echo $this->Html->link('Edit',array('controller' => 'SiteConfigurations'));
?></td></tr>
<tr><td>Please visit us at: </td><td><?php echo $this->Html->link('http://www.opencdrrate.org/','http://www.opencdrrate.org/', array('target' => '_blank'));?></td></tr>
<tr><td>Support: </td><td><?php echo $this->Html->link('https://sourceforge.net/p/opencdrrate/home/Support/','https://sourceforge.net/p/opencdrrate/home/Support/', array('target' => '_blank'));?> </td></tr>
<tr><td>Consulting Services: </td><td><?php echo $this->Html->link('https://sourceforge.net/p/opencdrrate/home/consulting services/','https://sourceforge.net/p/opencdrrate/home/consulting%20services/', array('target' => '_blank'));?> </td></tr>
<tr><td>Follow us on twitter for new feature notifications</td><td><?php echo $this->Html->link('https://twitter.com/opencdrrate/','https://twitter.com/opencdrrate/', array('target' => '_blank'));?> </td></tr>
</table>
<?php
if (Configure::read() > 0):
	Debugger::checkSecurityKeys();
endif;
?>
<p>
<?php
	if (is_writable(TMP)):

	else:
		echo '<span class="notice">';
			__('Your tmp directory is NOT writable.');
		echo '</span>';
	endif;
?>
</p>
<div id="url-rewriting-warning" style="background-color:#e32; color:#fff; padding:3px; margin: 20px 0">
	<?php __('URL rewriting is not properly configured on your server. '); ?>
	<ol style="padding-left:20px">
		<tr><td>
			<a target="_blank" href="http://book.cakephp.org/view/917/Apache-and-mod_rewrite-and-htaccess" style="color:#fff;">
				<?php __('Help me configure it')?>
			</a>
		</td></tr>
		<tr><td>
			<a target="_blank" href="http://book.cakephp.org/view/931/CakePHP-Core-Configuration-Variables" style="color:#fff;">
				<?php __('I don\'t / can\'t use URL rewriting')?>
			</a>
		</td></tr>
	</ol>
</div>
<p>
<?php
	$settings = Cache::settings();
	if (!empty($settings)):

	else:
		echo '<span class="notice">';
				__('Your cache is NOT working. Please check the settings in APP/config/core.php');
		echo '</span>';
	endif;
?>
</p>
<?php
if (!empty($filePresent)):
	if (!class_exists('ConnectionManager')) {
		require LIBS . 'model' . DS . 'connection_manager.php';
	}
	$db = ConnectionManager::getInstance();
 	$connected = $db->getDataSource('default');
?>
<p>
<?php
	if ($connected->isConnected()):

	else:
		echo '<span class="notice">';
			__('O is NOT able to connect to the database.');
		echo '</span>';
	endif;
?>
</p>
<?php endif;?>



