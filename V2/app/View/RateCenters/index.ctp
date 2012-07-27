<div class="tieredoriginationratecentermasters index">
	<h2><?php echo __('Rate Centers');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('ratecenter','Rate Center');?></th>
			<th><?php echo $this->Paginator->sort('tier');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($tieredoriginationratecentermasters as $tieredoriginationratecentermaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $tieredoriginationratecentermaster['Tieredoriginationratecentermaster']['ratecenter']; ?>&nbsp;</td>
		<td><?php echo $tieredoriginationratecentermaster['Tieredoriginationratecentermaster']['tier']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $tieredoriginationratecentermaster['Tieredoriginationratecentermaster']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $tieredoriginationratecentermaster['Tieredoriginationratecentermaster']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tieredoriginationratecentermaster['Tieredoriginationratecentermaster']['ratecenter'])); ?>
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
		<li><?php echo $this->Html->link(__('Add Rate Center', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Export to CSV', true), array('action' => 'tocsv')); ?></li>
	</ul>
</div>