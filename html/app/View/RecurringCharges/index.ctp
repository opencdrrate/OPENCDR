<div class="recurringchargemasters index">
	<h2><?php echo __('Recurring Charges');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('recurringchargeid','ChargeID');?></th>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('activationdate','Activation Date');?></th>
			<th><?php echo $this->Paginator->sort('deactivationdate','Deactivation Date');?></th>
			<th><?php echo $this->Paginator->sort('unitamount','Unit Amount');?></th>
			<th><?php echo $this->Paginator->sort('quantity');?></th>
			<th><?php echo $this->Paginator->sort('chargedesc','Description');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($recurringchargemasters as $recurringchargemaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['recurringchargeid']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['activationdate']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['deactivationdate']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['unitamount']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['quantity']; ?>&nbsp;</td>
		<td><?php echo $recurringchargemaster['Recurringchargemaster']['chargedesc']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $recurringchargemaster['Recurringchargemaster']['recurringchargeid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $recurringchargemaster['Recurringchargemaster']['recurringchargeid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $recurringchargemaster['Recurringchargemaster']['recurringchargeid'])); ?>
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
		<li><?php echo $this->Html->link(__('Add Recurring Charge', true), array('action' => 'add', $customerid)); ?></li>
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