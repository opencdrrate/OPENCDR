<?php
	$currency = $siteconfiguration['currency'];
	$currencySettings = $siteconfiguration['currencysettings'];
?>
<div class="customerretailplanmasters index">
	<h2><?php echo __('Retail Plans');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('planid','PlanID');?></th>
			<th><?php echo $this->Paginator->sort('plandescription','Description');?></th>
			<th><?php echo $this->Paginator->sort('servicefee','Service Fee');?></th>
			<th><?php echo $this->Paginator->sort('originationrate','Origination Rate');?></th>
			<th><?php echo $this->Paginator->sort('tollfreeoriginationrate','Toll-Free Origination Rate');?></th>
			<th><?php echo $this->Paginator->sort('freeminutespercycle','Free Minutes Per Cycle');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($retailplanmasters as $retailplanmaster):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $retailplanmaster['Retailplanmaster']['planid']; ?>&nbsp;</td>
		<td><?php echo $retailplanmaster['Retailplanmaster']['plandescription']; ?>&nbsp;</td>
		<td style="text-align:center;"><?php echo $this->Number->currency($retailplanmaster['Retailplanmaster']['servicefee'],$currency, $currencySettings); ?>&nbsp;</td>
		<td style="text-align:center;"><?php echo $retailplanmaster['Retailplanmaster']['originationrate']; ?>&nbsp;</td>
		<td style="text-align:center;"><?php echo $retailplanmaster['Retailplanmaster']['tollfreeoriginationrate']; ?>&nbsp;</td>
		<td style="text-align:center;"><?php echo $retailplanmaster['Retailplanmaster']['freeminutespercycle']; ?>&nbsp;</td>
		<td class="actions" style="text-align:center;">
			<?php echo $this->Html->link(__('View Rates', true), array('controller'=>'Retailplanterminationrate', 'action'=>'index',$retailplanmaster['Retailplanmaster']['planid'])); ?>
			<?php echo $this->Html->link(__('View Customers', true), array('controller'=>'Customerretailplans', 'action'=>'index',$retailplanmaster['Retailplanmaster']['planid'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $retailplanmaster['Retailplanmaster']['planid'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $retailplanmaster['Retailplanmaster']['planid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $retailplanmaster['Retailplanmaster']['planid'])); ?>
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
		<li><?php echo $this->Html->link(__('Create New Retail Plan', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Assign Plan to Customer', true), 
							array(
								'controller' => 'Customerretailplans'
								,'action' => 'add'
							)); ?></li>
	</ul>
</div>