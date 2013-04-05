<div class="callsbycustomerandtype index">
	<h2><?php echo __('Average monthly inbound calls');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>CarrierID</th>
			<th>Calls</th>
			<th>Raw Duration</th>
			<th>Billed Duration</th>
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
		<td><?php echo $item['Callspermonthpercarrier']['carrierid']?></td>
		<td><?php echo $this->Number->currency($item[0]['Calls'],'',array('places'=>0));?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['RawDuration']);?></td>
		<td><?php echo $this->TimeString->SecondsToString($item[0]['BilledDuration']);?></td>
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