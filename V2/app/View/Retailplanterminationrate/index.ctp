<div class="retailplanterminationrate index">
	<h2><?php __($planid.'\'s Retail Plans');?></h2>
	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('billedprefix','Billed Prefix');?></th>
			<th><?php echo $this->Paginator->sort('effectivedate','Effective Date');?></th>
			<th><?php echo $this->Paginator->sort('retailrate','Retail Rate');?></th>
			<th style="text-align:center;"><?php echo $this->Paginator->sort('canbefree','Can be free?');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($retailplanterminationrates as $retailplanterminationrate):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $retailplanterminationrate['Retailplanterminationrate']['billedprefix']; ?>&nbsp;</td>
		<td><?php echo $this->Time->format('Y-m-d',$retailplanterminationrate['Retailplanterminationrate']['effectivedate']); ?>&nbsp;</td>
		<td><?php echo $retailplanterminationrate['Retailplanterminationrate']['retailrate']; ?>&nbsp;</td>
		<td style="text-align:center;"><?php 
		if($retailplanterminationrate['Retailplanterminationrate']['canbefree']){
			echo 'Yes';
		}
		else{
			echo 'No';
		}
		?>&nbsp;</td>
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
		<li><?php echo $this->Html->link(__('Back', true), array('controller' => 'Retailplans')); ?></li>
		<li><?php echo $this->Html->link(__('Import', true), array('action' => 'import', $planid)); ?></li>
		<li><?php echo $this->Html->link(__('Export to CSV', true), array('action' => 'tocsv', $planid)); ?></li>
	</ul>
</div>