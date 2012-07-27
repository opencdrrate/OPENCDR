<div class="processhistories index">
	<h2><?php echo __('Process History');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('processname','Process Name');?></th>
			<th><?php echo $this->Paginator->sort('startdatetime','Start Time');?></th>
			<th><?php echo $this->Paginator->sort('enddatetime','End Time');?></th>
			<th><?php echo $this->Paginator->sort('duration');?></th>
			<th><?php echo $this->Paginator->sort('records');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($processhistories as $processhistory):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $processhistory['Processhistory']['processname']; ?>&nbsp;</td>
		<td><?php echo $processhistory['Processhistory']['startdatetime']; ?>&nbsp;</td>
		<td><?php echo $processhistory['Processhistory']['enddatetime']; ?>&nbsp;</td>
		<td><?php echo $processhistory['Processhistory']['duration']; ?>&nbsp;</td>
		<td><?php echo $this->Number->currency($processhistory['Processhistory']['records'],'',array('places'=>0)); ?>&nbsp;</td>
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
		<li><?php echo $this->Html->link('Back', '/');?></li>
		<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
	</ul>
</div>