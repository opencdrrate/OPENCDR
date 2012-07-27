<div class="paymentmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add Payment'); ?></legend>
	<?php
		echo $this->Form->input('customerid', array('value' => $customerid, 'type'=>'hidden'));
		echo $this->Form->input('paymentamount');
		echo $this->Form->input('paymentdate');
		echo $this->Form->input('paymenttype');
		echo $this->Form->input('paymentnote');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back to Customers', true), array('controller' => 'customers', 'action' => 'index')); ?> </li>
		
	</ul>
</div>