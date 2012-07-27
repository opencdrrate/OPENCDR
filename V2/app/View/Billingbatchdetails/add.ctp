<div class="billingbatchdetails form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add Billingbatchdetail'); ?></legend>
	<?php
		echo $this->Form->input('billingbatchid');
		echo $this->Form->input('customerid');
		echo $this->Form->input('calltype');
		echo $this->Form->input('lineitemtype');
		echo $this->Form->input('lineitemdesc');
		echo $this->Form->input('lineitemamount');
		echo $this->Form->input('lineitemquantity');
		echo $this->Form->input('periodstartdate');
		echo $this->Form->input('periodenddate');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Billingbatchdetails', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Customers', true), array('controller' => 'customers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Customer', true), array('controller' => 'customers', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Billingbatchmasters', true), array('controller' => 'billingbatchmasters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Billing Batch', true), array('controller' => 'billingbatchmasters', 'action' => 'add')); ?> </li>
	</ul>
</div>