<div class="callrecordmasterHelds index">
	<h2><?php echo __('Rating Errors');?></h2>
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
			<th><?php echo $this->Paginator->sort('errormessage','Error Message');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($callrecordmasterHelds as $callrecordmasterHeld):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['callid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['customerid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['calltype']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['calldatetime']; ?>&nbsp;</td>
		<td style="text-align:center"> <?php echo $this->TimeString->SecondsToString($callrecordmasterHeld['CallrecordmasterHeld']['duration']); ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['direction']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['sourceip']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['originatingnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['destinationnumber']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['routingprefix']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['lrn']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['cnamdipped']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['ratecenter']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['carrierid']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['wholesalerate']; ?>&nbsp;</td>
		<td><?php echo $callrecordmasterHeld['CallrecordmasterHeld']['wholesaleprice']; ?>&nbsp;</td>
		<td><?php echo $this->Html->link($callrecordmasterHeld['CallrecordmasterHeld']['errormessage'], 'https://sourceforge.net/p/opencdrrate/home/Rating%20Errors/'); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $callrecordmasterHeld['CallrecordmasterHeld']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $callrecordmasterHeld['CallrecordmasterHeld']['rowid']), null, sprintf(__('Are you sure you want to delete %s?', true), $callrecordmasterHeld['CallrecordmasterHeld']['callid'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
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
		<li><?php echo $this->Html->link(__('Move to Rating Queue',true), array('action' => 'moveToTBR')); ?></li>
		<li><?php echo $this->Html->link('HELP', 'https://sourceforge.net/p/opencdrrate/home/Rating Errors/' , array('target' => '_blank')); ?></li>
	</ul>
</div>