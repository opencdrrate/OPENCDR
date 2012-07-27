<?php
$currency = $siteconfiguration['currency'];
$currencySettings = $siteconfiguration['currencysettings'];
?>
<div class="customers index">
	<h2><?php echo __('Customers');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('customername','Customer Name');?></th>
			<th><?php echo $this->Paginator->sort('billingcycle','Billing Cycle');?></th>
			<th><?php echo $this->Paginator->sort('customertype','Customer Type');?></th>
			<th>One-Time Charges</th>
			<th>Recurring Charges</th>
			<th><?php echo __('Total Billed');?></th>
			<th><?php echo __('Unbilled CDR');?></th>
			<th><?php echo __('Payments');?></th>
			<th><?php echo __('Balance');?>
				<div id="tooltip2"><a>What's this?
							<span>
								A positive value indicates a debit balance. A negative value indicates 
								a credit balance.
							</span>
						</a>
				</div>
			</th>
			<th><?php echo __('Credit Limit');?></th>
			<th><?php echo __('Available Credit');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($customers as $customer):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<?php 
		$unbilledcdr = 0;
		if(isset($customer['Unbilledcdr']['UnbilledCDR'])){
			$unbilledcdr = $customer['Unbilledcdr']['UnbilledCDR'];
		}
		$balance =  $customer['Customer']['totalbilled'] - $customer['Customer']['totalpayments'] + $unbilledcdr;
		$availablecredit = '';
		if(isset($customer['Customer']['creditlimit'])){
			if($customer['Customer']['creditlimit'] > 0){
				$availablecredit = $customer['Customer']['creditlimit'] - $balance;
			}
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $customer['Customer']['customerid']; ?>&nbsp;</td>
		<td><?php echo $customer['Customer']['customername']; ?>&nbsp;</td>
		<td><?php echo $customer['Customer']['billingcycle']; ?>&nbsp;</td>
		<td><?php echo $customer['Customer']['customertype']; ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Onetimechargequeues', $customer['Customer']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'RecurringCharges', $customer['Customer']['customerid']));?></td>
		<td style="text-align:right"><?php echo $this->Number->currency($customer['Customer']['totalbilled'],$currency, $currencySettings); ?>&nbsp;</td>
		<td style="text-align:right"><?php echo $this->Number->currency($unbilledcdr,$currency, $currencySettings); ?>&nbsp;</td>
		<td style="text-align:right"><?php echo $this->Number->currency($customer['Customer']['totalpayments'],$currency, $currencySettings);?>&nbsp;</td>
		<td style="text-align:right"><?php echo $this->Number->currency($balance,$currency, $currencySettings); ?>&nbsp;</td>
		<td style="text-align:right"><?php if($customer['Customer']['creditlimit']){echo $this->Number->currency($customer['Customer']['creditlimit'],$currency, $currencySettings);} ?>&nbsp;</td>
		<td style="text-align:right"><?php if($availablecredit){echo $this->Number->currency($availablecredit,$currency, $currencySettings);} ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $customer['Customer']['customerid'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $customer['Customer']['customerid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $customer['Customer']['customerid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $customer['Customer']['customerid'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' =>  __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), '/'); ?></li>
		<li><?php echo $this->Html->link(__('Add Customer', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Export to CSV', true), array('action' => 'tocsv')); ?></li>
	</ul>
</div>