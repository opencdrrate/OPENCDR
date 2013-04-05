<div class="callsbycustomerandtype index">
	<h2><?php echo __('Inbound vs. Outbound minutes per customer');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>CustomerID</th>
			<th>CarrierID</th>
			<th>Year</th>
			<th>Month</th>
			<th>Inbound Calls</th>
			<th>Inbound RawDuration</th>
			<th>Inbound BilledDuration</th>
			<th>Outbound Calls</th>
			<th>Outbound RawDuration</th>
			<th>Outbound BilledDuration</th>
	</tr>
	<?php
	$i = 0;
	foreach ($data as $item):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $item['Calldirection']['customerid']?></td>
		<td><?php echo $item['Calldirection']['carrierid']?></td>
		<td><?php echo $item[0]['Year']?></td>
		<td><?php echo $item[0]['Month']?></td>
		<td><?php echo $this->Number->currency($item[0]['InboundCalls'],'',array('places'=>0));?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['InboundRawDuration']);?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['InboundBilledDuration']);?></td>
		<td><?php echo $this->Number->currency($item[0]['OutboundCalls'],'',array('places'=>0));?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['OutboundRawDuration']);?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['OutboundBilledDuration']);?></td>
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
		<li><?php echo $this->Html->link('Back', '/pages/reports');?></li>
		<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
	</ul>
</div>