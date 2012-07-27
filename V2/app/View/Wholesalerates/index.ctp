<div class="Wholesalerates index">
	<h2><?php echo __('Edit Customer Rates');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('customerid','CustomerID');?></th>
			<th>Interstate</th>
			<th>Intrastate</th>
			<th>Tiered Origination</th>
			<th>International</th>
			<th>Toll-free Origination</th>
			<th>Simple Termination</th>
	</tr>
	<?php
	$i = 0;
	foreach ($customers as $customer):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $customer['Wholesalerate']['customerid']; ?>&nbsp;</td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Interstaterates', $customer['Wholesalerate']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Intrastaterates', $customer['Wholesalerate']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Tieredoriginationrates', $customer['Wholesalerate']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Internationalrates', $customer['Wholesalerate']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Tollfreeoriginationrates', $customer['Wholesalerate']['customerid']));?></td>
		<td style="text-align:center"><?php echo $this->Html->link('Edit',array( 'controller' => 'Simpleterminationrates', $customer['Wholesalerate']['customerid']));?></td>
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
	</ul>
</div>

