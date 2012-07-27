<div class="onetimechargequeues index">
	<h2><?php echo __('One-time charges');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('onetimechargeid','ChargeID');?></th>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('chargedate','Charge Date');?></th>
			<th><?php echo $this->Paginator->sort('unitamount','Unit Amount');?></th>
			<th><?php echo $this->Paginator->sort('quantity');?></th>
			<th><?php echo $this->Paginator->sort('chargedesc','Description');?></th>
			<th><?php echo $this->Paginator->sort('billingbatchid','Billing BatchID');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($onetimechargequeues as $onetimechargequeue):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $onetimechargequeue['Onetimechargequeue']['onetimechargeid']; ?>&nbsp;</td>
		<td><?php echo $onetimechargequeue['Onetimechargequeue']['customerid']; ?>&nbsp;</td>
		<td><?php echo $onetimechargequeue['Onetimechargequeue']['chargedate']; ?>&nbsp;</td>
		<td style="text-align:right"><?php echo $onetimechargequeue['Onetimechargequeue']['unitamount']; ?>&nbsp;</td>
		<td style="text-align:right"><?php echo $onetimechargequeue['Onetimechargequeue']['quantity']; ?>&nbsp;</td>
		<td><?php echo $onetimechargequeue['Onetimechargequeue']['chargedesc']; ?>&nbsp;</td>
		<td><?php echo $onetimechargequeue['Onetimechargequeue']['billingbatchid']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $onetimechargequeue['Onetimechargequeue']['onetimechargeid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $onetimechargequeue['Onetimechargequeue']['onetimechargeid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $onetimechargequeue['Onetimechargequeue']['onetimechargeid'])); ?>
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
		<li><?php echo $this->Html->link(__('Back', true), '/Customers'); ?></li>
		<li><?php echo $this->Html->link(__('Add one-time charge', true), array('action' => 'add', $customerid)); ?></li>
	<?php
		if(isset($customerid)){
		?>
			<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv', $customerid)); ?></li>
		<?php
		}
		else{
		?>
			<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
		<?php
		}
	?>
	</ul>
</div>