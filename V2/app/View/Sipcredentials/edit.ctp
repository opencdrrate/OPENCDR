O<div class="sipcredential form">
<?php echo $this->Form->create('', array('action' => 'edit'));?>
	<fieldset>
		<legend><?php echo __('Edit SIP Credentials for '. $customerid); ?></legend>
		<?php
		echo $this->Form->input('customerid', array('value'=>$customerid, 'type'=>'hidden'));
		echo $this->Form->input('sipusername',array('label' => 'SIP User Name'));
		echo $this->Form->input('sippassword',array('label' => 'SIP Password', 'type' => 'password'));
		?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back to Customers', array('controller' => 'customers', 'action'=> 'view', $customerid));?></li>
	</ul>
</div>