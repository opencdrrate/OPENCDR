<?php
$currency = $siteconfiguration['currency'];
$currencySettings = $siteconfiguration['currencysettings'];
?>
<div class="billingdetailssummaries index">
	<h2><?php echo __('Billing batch details');?></h2>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th>CustomerID</th>
			<th>Total</th>
			<th>Line Items</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th class="actions"><?php echo __('Actions');?></th>
		</tr>
	<?php
	$i = 0;
	foreach ($billingdetailsummaries as $summary):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $summary['Billingbatchdetail']['customerid'];?></td>
		<td style="text-align:right"><?php echo $this->Number->currency($summary[0]['totalbilled'], $currency,$currencySettings);?></td>
		<td style="text-align:center"><?php echo $summary[0]['lineitems'];?></td>
		<td><?php echo $summary[0]['periodstartdate'];?></td>
		<td><?php echo $summary[0]['periodenddate'];?></td>
		<td class="actions">
			<?php //echo $this->Html->link(__('Download Invoice as PDF', true), array('action'=>'pdf', $summary['Billingbatchdetail']['billingbatchid'], $summary['Billingbatchdetail']['customerid'])); ?>
			<?php echo $this->Html->link(__('View PDF', true), array('action'=>'pdf', $summary['Billingbatchdetail']['billingbatchid'], $summary['Billingbatchdetail']['customerid'])); ?>
			<?php echo $this->Html->link(__('Print Invoice to Screen', true), array('action'=>'invoice', $summary['Billingbatchdetail']['billingbatchid'], $summary['Billingbatchdetail']['customerid'])); ?>
			<?php echo $this->Html->link(__('Export to Quickbooks', true),array('action'=>'quickbooks', $summary['Billingbatchdetail']['billingbatchid'], $summary['Billingbatchdetail']['customerid'])); ?>
			<?php echo $this->Html->link(__('View Details', true),  array( 'controller' => 'Billingbatchdetails', 'action'=>'index', $summary['Billingbatchdetail']['billingbatchid'], $summary['Billingbatchdetail']['customerid'])); ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
<ul>
	<li>
		<?php echo $this->Html->link(__('Back', true), array('controller'=>'Billingbatches','action'=>'index')); ?>
	</li>
	<li>
		<?php echo $this->Html->link(__('Export all to Quickbooks', true), array('controller'=>'Billingdetailsummary','action'=>'allquickbooks',$batchid)); ?>
	</li>
	<li>
		<?php echo $this->Html->link(__('Email All Invoices', true), array('controller'=>'Billingdetailsummary','action'=>'SendInvoices',$batchid), null, sprintf(__('This will send an email to all your customers.  Are you sure?', true))); ?>
	</li>
</ul>
</div>