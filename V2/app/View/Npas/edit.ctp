<div class="npamasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit NPA'); ?></legend>
		<label>NPA</label><div class="readonly"><?php echo $npamaster['Npamaster']['npa'];?></div>
	<?php
		echo $this->Form->input('state', array('style'=>'width:400px;'));
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