<div class="tieredoriginationratemasters index">
	<h2><?php echo __('Tiered origination rates');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th><?php echo $this->Paginator->sort('tier');?></th>
			<th><?php echo $this->Paginator->sort('effectivedate','Effective Date');?></th>
			<th><?php echo $this->Paginator->sort('retailrate','Retail Rate');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($tieredoriginationratemasters as $tieredoriginationratemaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $tieredoriginationratemaster['Tieredoriginationratemaster']['customerid']; ?>&nbsp;</td>
		<td><?php echo $tieredoriginationratemaster['Tieredoriginationratemaster']['tier']; ?>&nbsp;</td>
		<td><?php echo $tieredoriginationratemaster['Tieredoriginationratemaster']['effectivedate']; ?>&nbsp;</td>
		<td><?php echo $tieredoriginationratemaster['Tieredoriginationratemaster']['retailrate']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $tieredoriginationratemaster['Tieredoriginationratemaster']['rowid'], $customerid), null, sprintf(__('Are you sure you want to delete # %s?', true), $tieredoriginationratemaster['Tieredoriginationratemaster']['rowid'])); ?>
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