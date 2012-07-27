<div class="callrecordmasters index">
	<h2><?php echo __('Rated Calls');?></h2>
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
			<th><?php echo $this->Paginator->sort('Billed Duration');?></th>
			<th><?php echo $this->Paginator->sort('direction');?></th>
			<th><?php echo $this->Paginator->sort('SourceIP');?></th>
			<th><?php echo $this->Paginator->sort('originatingnumber','Originating Number');?></th>
			<th><?php echo $this->Paginator->sort('destinationnumber','Destination Number');?></th>
			<th><?php echo $this->Paginator->sort('routingprefix','Routing Prefix');?></th>
			<th><?php echo $this->Paginator->sort('LRN');?></th>
			<th><?php echo $this->Paginator->sort('lrndipfee');?></th>
			<th><?php echo $this->Paginator->sort('billednumber','Billed Number');?></th>
			<th><?php echo $this->Paginator->sort('billedprefix','Billed Prefix');?></th>
			<th><?php echo $this->Paginator->sort('RatedDateTime');?></th>
			<th><?php echo $this->Paginator->sort('Retail Rate');?></th>
			<th><?php echo $this->Paginator->sort('CNAM Dipped');?></th>
			<th><?php echo $this->Paginator->sort('CNAM Fee');?></th>
			<th><?php echo $this->Paginator->sort('billedtier','Billed Tier');?></th>
			<th><?php echo $this->Paginator->sort('ratecenter','Rate Center');?></th>
			<th><?php echo $this->Paginator->sort('billingbatchid','Billing BatchID');?></th>
			<th><?php echo $this->Paginator->sort('retailprice','Retail Price');?></th>
			<th><?php echo $this->Paginator->sort('canbefree','Can Be Free');?></th>
			<th><?php echo $this->Paginator->sort('CarrierID');?></th>
			<th><?php echo $this->Paginator->sort('wholesalerate','Wholesale Rate');?></th>
			<th><?php echo $this->Paginator->sort('wholesaleprice','Wholesale Price');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($data as $callrecordmaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $callrecordmaster['Callrecordmaster']['callid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['calltype']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['calldatetime']; ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->TimeString->SecondsToString($callrecordmaster['Callrecordmaster']['duration']); ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->TimeString->SecondsToString($callrecordmaster['Callrecordmaster']['billedduration']); ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $callrecordmaster['Callrecordmaster']['direction']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['sourceip']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['originatingnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['destinationnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['routingprefix']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['lrn']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['lrndipfee']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['billednumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['billedprefix']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['rateddatetime']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['retailrate']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['cnamdipped']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['cnamfee']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['billedtier']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['ratecenter']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['billingbatchid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['retailprice']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['canbefree']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['carrierid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['wholesalerate']; ?>&nbsp;</td>
		<td><?php echo $callrecordmaster['Callrecordmaster']['wholesaleprice']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $callrecordmaster['Callrecordmaster']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $callrecordmaster['Callrecordmaster']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $callrecordmaster['Callrecordmaster']['callid'])); ?>
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
	<li><?php echo $this->Html->link(__('Export to CSV', true), array('action' => 'tocsv')); ?></li>
	<li><?php echo $this->Html->link(__('Export to SLINGER', true), array('action' => 'tocsv')); ?></li>
	<li><?php echo $this->Html->link(__('Export to PIPE', true), array('action' => 'topipe')); ?></li>
	</ul>
</div>