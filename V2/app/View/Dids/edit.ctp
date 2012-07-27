<div class="didmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit DID'); ?></legend>
		<label>DID</label><div class="readonly"><?php echo $did['Didmaster']['did']?></div>
	<?php
		echo $this->Form->input(
			'customerid',
			array(
				'options' => $customers,
				'type' => 'select',
				'empty' => '-- Select a Customer --',
				'selected' => $this->data['Didmaster']['customerid'],
				'label' => 'Customer'
			)
		);
		echo $this->Form->input('sipusername',array('label' => 'SIP Username'));
		echo $this->Form->input(
			'sippassword',
			array(
				'type' => 'password',
				'label' => 'SIP Password'
			)
		);
		if($this->data['Didmaster']['sipstatus'] == 'active'){
			$realstatus = 1;
		}
		else if($this->data['Didmaster']['sipstatus'] == 'inactive'){
			$realstatus = 0;
		}
		echo $this->Form->input(
			'sipstatus',
			array(
				'options' => array('0' => 'inactive', '1' => 'active'),
				'type' => 'select',
				'empty'=>'-- Select a SIPStatus --',
				'selected' => $realstatus,
				'label' => 'SIP Status'
			)
		);
		echo $this->Form->input('rowid', array('type'=>'hidden'));
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