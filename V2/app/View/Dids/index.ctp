<div class="didmasters index">
	<h2><?php echo __('DID List');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('did','DID');?></th>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('sipusername','SIP Username');?></th>
			<th><?php echo $this->Paginator->sort('sipstatus','SIP Status');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($didmasters as $didmaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $didmaster['Didmaster']['did']; ?>&nbsp;</td>
		<td><?php echo $didmaster['Didmaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $didmaster['Didmaster']['sipusername']; ?>&nbsp;</td>
		<td><?php echo $didmaster['Didmaster']['sipstatus']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $didmaster['Didmaster']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $didmaster['Didmaster']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $didmaster['Didmaster']['did'])); ?>
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
		<li><?php echo $this->Html->link(__('Add DID', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Add VoIP Innovations DID', true), array('action' => 'addVoip')); ?></li>
		<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
	</ul>
</div>