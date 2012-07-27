<div class="customerretailplanmasters index">
	<h2><?php echo __('Customer Retail Plans');?></h2>
	<p>
Only customers with a type of retail will be listed on this page.
	</p>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('planid','PlanID');?></th>
			<th><?php echo $this->Paginator->sort('activationdate','Activation Date');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($customerretailplanmasters as $customerretailplanmaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $customerretailplanmaster['Customerretailplanmaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $customerretailplanmaster['Customerretailplanmaster']['planid']; ?>&nbsp;</td>
		<td><?php echo $customerretailplanmaster['Customerretailplanmaster']['activationdate']; ?>&nbsp;</td>
		<td class="actions">
		<?php echo $this->Html->link(__('Unassign', true), array('action' => 'delete', $customerretailplanmaster['Customerretailplanmaster']['rowid']), null, __('Are you sure?', true)); ?>
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
		<li><?php echo $this->Html->link(__('Back', true), array('controller'=>'Retailplans')); ?></li>
	</ul>
</div>