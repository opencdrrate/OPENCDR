<div class="customercontactinformation form">
<?php echo $this->Form->create('Contactinformation', 
	array(
		'url' => array(
			'controller' => 'customercontactinformations', 
			'action' => 'edit'
		)
	)	
);?>

	<fieldset>
		<legend><?php echo __('Edit Contact Information'); ?></legend>
	<?php
		echo $this->Form->input('customerid');
		echo $this->Form->input('primaryemailaddress', array('label' => 'Primary Email Address'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back to Customers', array('controller' => 'customers','action'=> 'view', $this->data['Contactinformation']['customerid']));?></li>
	</ul>
</div>