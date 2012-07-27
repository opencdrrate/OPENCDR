<div class="customercontactinformation form">
<?php echo $this->Form->create('Contactinformation', 
	array(
		'url' => array(
			'controller' => 'customercontactinformations', 
			'action' => 'add'
		)
	)	
);?>
	<fieldset>
		<legend><?php echo __('Add Contact Information'); ?></legend>
	<?php
		echo $this->Form->input('customerid', array('value'=>$customerid));
		echo $this->Form->input('primaryemailaddress', array('label' => 'Primary Email Address'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back to Customers', array('controller' => 'customers','action'=> 'view', $customerid));?></li>
	</ul>
</div>