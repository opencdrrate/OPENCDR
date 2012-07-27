<div class="billingbatchdetails index">
	<h2><?php echo __('Billing Details');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('lineitemdesc');?></th>
			<th><?php echo $this->Paginator->sort('lineitemamount');?></th>
			<th><?php echo $this->Paginator->sort('periodstartdate');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($billingbatchdetails as $billingbatchdetail):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $billingbatchdetail['Billingbatchdetail']['lineitemdesc']; ?>&nbsp;</td>
		<td><?php echo $billingbatchdetail['Billingbatchdetail']['lineitemamount']; ?>&nbsp;</td>
		<td><?php echo $billingbatchdetail['Billingbatchdetail']['periodstartdate']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $billingbatchdetail['Billingbatchdetail']['rowid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $billingbatchdetail['Billingbatchdetail']['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $billingbatchdetail['Billingbatchdetail']['rowid'])); ?>
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
		<li><?php echo $this->Html->link(__('Back', true), array('controller' => 'billingdetailsummary', 'action' => 'index', $billingbatchid)); ?> </li>
	</ul>
</div>