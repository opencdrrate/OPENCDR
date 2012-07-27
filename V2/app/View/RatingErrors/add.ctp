<div class="callrecordmasterHelds form">
<?php echo $this->Form->create('CallrecordmasterHeld');?>
	<fieldset>
		<legend><?php echo __('Add Callrecordmaster Held'); ?></legend>
	<?php
		echo $this->Form->input('customerid');
		echo $this->Form->input('calltype');
		echo $this->Form->input('calldatetime');
		echo $this->Form->input('duration');
		echo $this->Form->input('direction');
		echo $this->Form->input('sourceip');
		echo $this->Form->input('originatingnumber');
		echo $this->Form->input('destinationnumber');
		echo $this->Form->input('lrn');
		echo $this->Form->input('cnamdipped');
		echo $this->Form->input('ratecenter');
		echo $this->Form->input('carrierid');
		echo $this->Form->input('wholesalerate');
		echo $this->Form->input('wholesaleprice');
		echo $this->Form->input('errormessage');
		echo $this->Form->input('rowid');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Callrecordmaster Helds', true), array('action' => 'index'));?></li>
	</ul>
</div>