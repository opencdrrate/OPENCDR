<div class="concurrentcallspeak index">
	<h2><?php echo __('Concurrent Calls : Peak and Average per day, carrier');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Date</th>
			<th>CarrierID</th>
			<th>Direction</th>
			<th>Peak</th>
			<th>Average</th>
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
		<td><?php echo $item['Concurrentcallscarrierreport']['Date']?></td>
		<td><?php echo $item['Concurrentcallscarrierreport']['carrierid']?></td>
		<td><?php echo $item['Concurrentcallscarrierreport']['direction']?></td>
		<td><?php echo $item[0]['Peak']?></td>
		<td><?php echo $item[0]['Average']?></td>
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