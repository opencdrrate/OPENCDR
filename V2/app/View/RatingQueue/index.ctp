<?php echo $this->Html->script('jquery-1.7.2');?>
<?php echo $this->Html->script('jquery/rate_calls');?>

<div class="callrecordmasterTbrs index">
	<h2><?php echo __('Rating Queue');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('callid','CallID');?></th>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('calltype','Call Type');?></th>
			<th><?php echo $this->Paginator->sort('calldatetime','CallDateTime');?></th>
			<th><?php echo $this->Paginator->sort('duration');?></th>
			<th><?php echo $this->Paginator->sort('direction');?></th>
			<th><?php echo $this->Paginator->sort('sourceip','SourceIP');?></th>
			<th><?php echo $this->Paginator->sort('originatingnumber','Originating Number');?></th>
			<th><?php echo $this->Paginator->sort('destinationnumber','Destination Number');?></th>
			<th><?php echo $this->Paginator->sort('routingprefix','Routing Prefix');?></th>
			<th><?php echo $this->Paginator->sort('lrn','LRN');?></th>
			<th><?php echo $this->Paginator->sort('cnamdipped','CNAM Dipped');?></th>
			<th><?php echo $this->Paginator->sort('ratecenter','Rate Center');?></th>
			<th><?php echo $this->Paginator->sort('carrierid','CarrierID');?></th>
			<th><?php echo $this->Paginator->sort('wholesalerate','Wholesale Rate');?></th>
			<th><?php echo $this->Paginator->sort('wholesaleprice','Wholesale Price');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($callrecordmasterTbrs as $callrecordmasterTbr):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['callid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['customerid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['calltype']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['calldatetime']; ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->TimeString->SecondsToString($callrecordmasterTbr['CallrecordmasterTbr']['duration']); ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['direction']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['sourceip']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['originatingnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['destinationnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['routingprefix']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['lrn']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['cnamdipped']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['ratecenter']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['carrierid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['wholesalerate']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterTbr['CallrecordmasterTbr']['wholesaleprice']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $callrecordmasterTbr['CallrecordmasterTbr']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $callrecordmasterTbr['CallrecordmasterTbr']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $callrecordmasterTbr['CallrecordmasterTbr']['callid'])); ?>
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
		<li><?php echo $this->Html->link('Back', '/');?></li>
		<li><?php echo $this->Html->link(__('Import New CDRs', true), array('action' => 'import')); ?></li>
		<li><?php echo $this->Html->link(__('Rate Calls', true), 'javascript:rate_calls()', null, sprintf(__('This will rate 1 day worth of CDR for each call type. This may take several minutes to run.', true))); ?></li>
		<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
		<li><div class="progress"></div></li>
		</ul>
</div>