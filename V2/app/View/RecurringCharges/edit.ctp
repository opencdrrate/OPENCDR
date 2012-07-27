<div class="recurringchargemasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit Charge'); ?></legend>
	<?php
		if(!isset($customerid)){
			$customerid = $this->data['Recurringchargemaster']['customerid'];
		}
		echo $this->Form->input('recurringchargeid');
		echo $this->Form->input('customerid', array('type' =>'hidden', 'value'=>$customerid));
		echo $this->Form->input('activationdate');
		echo $this->Form->input('deactivationdate');
		echo $this->Form->input('unitamount', array(
									'style' =>'width:400px',
									'label' => 'Unit Amount'
								)
							);
		echo $this->Form->input('quantity',array('style' =>'width:400px'));
		echo $this->Form->input('chargedesc', array('label' => 'Description'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul><li><?php echo $this->Html->link(__('Back', true), array('action' => 'index', $customerid));?></li>
	</ul>
</div>