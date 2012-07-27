<div class="billingbatchdetails form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit Detail'); ?></legend>
	<?php
		echo $this->Form->input('billingbatchid', array('type'=>'hidden'));
		echo $this->Form->input('customerid', array('type'=>'hidden'));
		echo $this->Form->input('calltype', array('type'=>'hidden'));
		echo $this->Form->input('lineitemtype', array('type'=>'hidden'));
		echo $this->Form->input('lineitemdesc');
		echo $this->Form->input('lineitemamount');
		echo $this->Form->input('lineitemquantity', array('type'=>'hidden'));
		echo $this->Form->input('periodstartdate');
		echo $this->Form->input('periodenddate', array('type'=>'hidden'));
		echo $this->Form->input('rowid');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), array(
			'action' => 'index',
			$this->data['Billingbatchdetail']['billingbatchid'],
			$this->data['Billingbatchdetail']['customerid']));?></li>
	</ul>
</div>