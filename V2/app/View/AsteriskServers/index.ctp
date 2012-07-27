
<div class="asteriskservers index">
	<h2><?php echo __('Asterisk Servers');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('servername','Server Name');?></th>
			<th><?php echo $this->Paginator->sort('serveripordns','IP Address or DNS');?></th>
			<th><?php echo $this->Paginator->sort('mysqlport','Port');?></th>
			<th><?php echo $this->Paginator->sort('mysqllogin','Login');?></th>
			<th><?php echo $this->Paginator->sort('cdrdatabase','Database');?></th>
			<th><?php echo $this->Paginator->sort('cdrtable','Table');?></th>
			<th><?php echo $this->Paginator->sort('active','Active');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($asteriskservers as $server):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<?php 
		$active = '';
		if($server['AsteriskServer']['active'] == 1){
			$active = 'Yes';
		}
		else{
			$active = 'No';
		}
		?>
		<td><?php echo $server['AsteriskServer']['servername']; ?>&nbsp;</td>
		<td><?php echo $server['AsteriskServer']['serveripordns']; ?>&nbsp;</td>
		<td><?php echo $server['AsteriskServer']['mysqlport']; ?>&nbsp;</td>
		<td><?php echo $server['AsteriskServer']['mysqllogin']; ?>&nbsp;</td>
		<td><?php echo $server['AsteriskServer']['cdrdatabase']; ?>&nbsp;</td>
		<td><?php echo $server['AsteriskServer']['cdrtable']; ?>&nbsp;</td>
		<td style="text-align=center;"><?php echo $active; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $server['AsteriskServer']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $server['AsteriskServer']['rowid']), null, sprintf(__('Are you sure you want to delete %s?', true), $server['AsteriskServer']['servername'])); ?>
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
		<li><?php echo $this->Html->link(__('Add Server', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Import CDR from All', true), array('action' => 'massimport')); ?></li>
		<li><?php echo $this->Html->link(__('Reset AMA Flags', true), array('action' => 'resetamaflags'), null, __('This will reset all your MySQL tables.  Are you sure?', true)); ?></li>
	</ul>
</div>