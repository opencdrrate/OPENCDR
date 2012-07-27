<fieldset>
<legend><?php echo __('Select a Customer');?></legend>
	<?php
		echo $this->Form->input(
			'customerid',
			array(
				'options' => $customers,
				'type' => 'select',
				'empty' => '-- Select a Customer --',
				'label' => 'Customer'
			)
		);?>
		
	<?php echo $this->Form->input('EPG', array('label'=>'Enter E.P.G.','type' => 'text', 'style'=>'width:400px;')); ?>
</fieldset>
<fieldset>
<legend><?php echo __('Enter their E911 address');?></legend>
	<?php echo $this->Form->input('callerName', array('label'=>'Customer Name','type' => 'text', 'style'=>'width:400px;')); ?>
	<?php echo $this->Form->input('Address1', array('label'=>'Address1','type' => 'text', 'style'=>'width:400px;')); ?>
	<?php echo $this->Form->input('Address2', array('label'=>'Address2','type' => 'text', 'style'=>'width:400px;')); ?>
	<?php echo $this->Form->input('city', array('label'=>'City','type' => 'text', 'style'=>'width:400px;')); ?>
	<?php echo $this->Form->input('state', array('label'=>'State/Prov','type' => 'text', 'style'=>'width:400px;')); ?>
	<?php echo $this->Form->input('zip', array('label'=>'Zip/Postal Code','type' => 'text', 'style'=>'width:400px;')); ?>
</fieldset>
<?php echo $this->Form->submit('Buy DIDs');?>
<?php echo $this->Form->end(); ?>