<div class="tollfreeoriginationratemasters index">
	<h2><?php echo __('Toll-free origination rates');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('billedprefix','Billed Prefix');?></th>
			<th><?php echo $this->Paginator->sort('effectivedate','Effective Date');?></th>
			<th><?php echo $this->Paginator->sort('retailrate','Retail Rate');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($tollfreeoriginationratemasters as $tollfreeoriginationratemaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['billedprefix']; ?>&nbsp;</td>
		<td><?php echo $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['effectivedate']; ?>&nbsp;</td>
		<td><?php echo $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['retailrate']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['rowid'], $customerid), null, sprintf(__('Are you sure you want to delete # %s?', true), $tollfreeoriginationratemaster['Tollfreeoriginationratemaster']['rowid'])); ?>
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
		<li><?php echo $this->Html->link(__('Back', true), '/Wholesalerates'); ?></li>
		<li><?php echo $this->Html->link('Sample rate sheets', 'http://sourceforge.net/projects/opencdrrate/files/Sample%20Rate%20Sheets/', array('target' => '_blank'));?></li>
		<?php
			if(isset($customerid)){
			?>
				<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv', $customerid)); ?></li>
			<?php
			}
			else{
			?>
				<li><?php echo $this->Html->link(__('Export Table', true), array('action' => 'tocsv')); ?></li>
			<?php
			}
		?>
		<li><?php echo $this->Html->link('Import from CSV', array('action'=>'import',$customerid));?></li>
	</ul>
</div>