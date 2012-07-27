<div class="customertaxsetups form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit Tax Setup'); ?></legend>
	<label>Customer</label><div class="readonly"><?php echo $customertaxsetup['Customertaxsetup']['customerid']; ?></div>
	<label>Call Type</label><div class="readonly"><?php echo $customertaxsetup['Customertaxsetup']['calltype']; ?></div>
	<label>Tax Type</label><div class="readonly"><?php echo $customertaxsetup['Customertaxsetup']['taxtype']; ?></div>
	<?php
		echo $this->Form->input('taxrate', array('label'=>'Tax Rate','style' =>'width:400px'));
		echo $this->Form->input('rowid');
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