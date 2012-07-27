<div class="recurringchargemasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add Recurring Charge to ' . $customerid); ?></legend>
	<?php
		echo $this->Form->input('customerid', array('value' => $customerid, 'type' => 'hidden'));
		echo $this->Form->input('activationdate');
		echo $this->Form->input('deactivationdate');
		echo $this->Form->input('unitamount', array(
									'style' =>'width:400px',
									'label' => 'Unit Amount'
								)
							);
		echo $this->Form->input('quantity',
								array(
									'style' =>'width:400px',
									'default' => '1'
								)
							);
		echo $this->Form->input('chargedesc', array('label' => 'Description'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index', $customerid));?></li>
	</ul>
</div>