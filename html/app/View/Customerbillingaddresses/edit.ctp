<div class="customerbillingaddressmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit Billing Address'); ?></legend>
	<?php
		echo $this->Form->input('customerid');
		echo $this->Form->input('address1');
		echo $this->Form->input('address2');
		echo $this->Form->input('city');
		echo $this->Form->input('stateorprov', array('label' => 'State / Province'));
		echo $this->Form->input('country');
		echo $this->Form->input('zipcode', array('label' => 'Zip / Postal Code'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), array('controller' => 'customers', 'action' => 'index')); ?> </li>
	</ul>
</div>