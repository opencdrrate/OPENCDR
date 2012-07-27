<div class="uncategorizedCdrs index">
	<h2><?php echo __('Uncategorized CDRs',true);?></h2>
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
			<th><?php echo $this->Paginator->sort('calldatetime','CallDateTime');?></th>
			<th><?php echo $this->Paginator->sort('duration');?></th>
			<th><?php echo $this->Paginator->sort('direction');?></th>
			<th><?php echo $this->Paginator->sort('sourceip','SourceIP');?></th>
			<th><?php echo $this->Paginator->sort('originatingnumber','Originating Number');?></th>
			<th><?php echo $this->Paginator->sort('destinationnumber','Destination Number');?></th>
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
	foreach ($uncategorizedCdrs as $uncategorizedCdr):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['callid']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['customerid']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['calldatetime']; ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->TimeString->SecondsToString($uncategorizedCdr['UncategorizedCdr']['duration']); ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['direction']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['sourceip']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['originatingnumber']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['destinationnumber']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['lrn']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['cnamdipped']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['ratecenter']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['carrierid']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['wholesalerate']; ?>&nbsp;</td>
		<td><?php echo $uncategorizedCdr['UncategorizedCdr']['wholesaleprice']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $uncategorizedCdr['UncategorizedCdr']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $uncategorizedCdr['UncategorizedCdr']['callid'])); ?>
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
		<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
		<li><?php echo $this->Html->link(__('Categorize CDRs', true), array('action' => 'categorizecdrs')); ?></li>
	</ul>
</div>