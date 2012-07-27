<div class="voipdidstatus index">
	<h2><?php echo __('Add Status');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Telephone Number</th>
			<th>Action Taken</th>
			<th>Status</th>
	</tr>
	<?php
	$i = 0;
	foreach ($statuses as $status):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $status['tn']; ?>&nbsp;</td>
		<td><?php echo $status['actionMessage']; ?>&nbsp;</td>
		<td><?php echo $status['status']; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back to Main', true), array('action' => 'index')); ?></li>
	</ul>
</div>