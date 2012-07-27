<div class="onetimechargequeues form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit one-time charge'); ?></legend>
	<?php
		if(!isset($customerid)){
			$customerid = $this->data['Onetimechargequeue']['customerid'];
		}
		echo $this->Form->input('onetimechargeid');
		echo $this->Form->input('customerid', array('type' => 'hidden'));
		echo $this->Form->input('chargedate');
		echo $this->Form->input('unitamount', array('label'=>'Unit Amount','style' =>'width:400px'));
		echo $this->Form->input('quantity',array('style' =>'width:400px'));
		echo $this->Form->input('chargedesc', array('label'=>'Description'));
		echo $this->Form->input('billingbatchid');
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