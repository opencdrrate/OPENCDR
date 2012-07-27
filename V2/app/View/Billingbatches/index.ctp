<div class="billingbatchmasters index">
	<h2><?php echo __('Billing Batches');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('billingbatchid','Billing BatchID');?></th>
			<th><?php echo $this->Paginator->sort('billingdate','Billing Date');?></th>
			<th><?php echo $this->Paginator->sort('duedate','Due Date');?></th>
			<th><?php echo $this->Paginator->sort('billingcycleid','Billing CycleID');?></th>
			<th><?php echo $this->Paginator->sort('usageperiodend','Usage Period End Date');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($billingbatchmasters as $billingbatchmaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $billingbatchmaster['Billingbatchmaster']['billingbatchid']; ?>&nbsp;</td>
		<td><?php echo $billingbatchmaster['Billingbatchmaster']['billingdate']; ?>&nbsp;</td>
		<td><?php echo $billingbatchmaster['Billingbatchmaster']['duedate']; ?>&nbsp;</td>
		<td><?php echo $billingbatchmaster['Billingbatchmaster']['billingcycleid']; ?>&nbsp;</td>
		<td><?php echo $billingbatchmaster['Billingbatchmaster']['usageperiodend']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true),array( 'controller' => 'Billingdetailsummary',$billingbatchmaster['Billingbatchmaster']['billingbatchid']) ); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $billingbatchmaster['Billingbatchmaster']['billingbatchid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $billingbatchmaster['Billingbatchmaster']['billingbatchid'])); ?>
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
		<li><?php echo $this->Html->link(__('Generate Billing Batch', true), array('action' => 'add')); ?></li>
	</ul>
</div>