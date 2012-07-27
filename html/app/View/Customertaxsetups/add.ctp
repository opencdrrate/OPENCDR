<div class="customertaxsetups form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add Tax Setup'); ?></legend>
	<?php
		echo $this->Form->input(
			'customerid',
			array(
				'options' => $customers,
				'type' => 'select',
				'empty' => '-- Select a Customer --',
				'label' => 'Customer'
			)
		);
		
		echo $this->Form->input(
			'calltype',
			array(
				'options' => $calltypes,
				'type' => 'select',
				'empty' => '-- Choose a call type --',
				'label' => 'Call Type'
			)
		);
		echo $this->Form->input('taxtype', array('label'=>'Enter name of tax'));
		echo $this->Form->input('taxrate', array('label'=>'Tax Rate','default'=>'0.0000000','style' =>'width:400px'));
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