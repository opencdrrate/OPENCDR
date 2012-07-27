
	<h2><?php echo __('VOIP Dids');?></h2>
	<?php echo $this->Form->create('dids', array( 'action' => 'choosecustomer')); ?>
	<?php echo $this->Form->submit('Buy DIDs');?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Select</th>
			<th>E911</th>
			<th><?php echo 'Telephone Number';?></th>
			<th><?php echo 'Rate Center';?></th>
			<th><?php echo 'State';?></th>
			<th><?php echo 'Tier';?></th>
			<th><?php echo 'LATA ID';?></th>
			<th><?php echo 'Outbound CNAM';?></th>
			<th><?php echo 'T38';?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($Dids as $did):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Form->checkbox('did-'.$i, array('hiddenField' => false, 'value' => $did->tn)); ?>&nbsp;</td>
		<td><?php echo $this->Form->checkbox('E911-'.$i, array('hiddenField' => false, 'value' => $did->tn)); ?></td>
		<td><?php echo $did->tn ?>&nbsp;</td>
		<td><?php echo $did->rateCenter ?>&nbsp;</td>
		<td><?php echo $did->state ?>&nbsp;</td>
		<td><?php echo $did->tier ?>&nbsp;</td>
		<td><?php echo $did->lataId ?>&nbsp;</td>
		<td><?php echo $did->outboundCNAM ?>&nbsp;</td>
		<td><?php echo $did->t38 ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	<?php echo $this->Form->end(); ?>
	</table>