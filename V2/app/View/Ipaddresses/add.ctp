<div class="ipaddressmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add IP Address'); ?></legend>
	<?php
		echo $this->Form->input('ipaddress', array('label'=>'IP Address','style'=>'width:400px;'));
		echo $this->Form->input(
			'customerid',
			array(
				'options' => $customers,
				'type' => 'select',
				'empty' => '-- Select a Customer --',
				'label' => 'Customer'
			)
		);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index'));?></li>
	</ul>
</div>