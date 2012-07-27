<div class="callrecordmasterTbrs form">
<?php echo $this->Form->create('CallrecordmasterTbr');?>
	<fieldset>
		<legend><?php echo __('Edit Callrecordmaster Tbr'); ?></legend>
	<?php
		echo $this->Form->input('callid');
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
		echo $this->Form->input('rowid');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('CallrecordmasterTbr.callid')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('CallrecordmasterTbr.callid'))); ?></li>
		<li><?php echo $this->Html->link(__('List Callrecordmaster Tbrs', true), array('action' => 'index'));?></li>
	</ul>
</div>