<div class="npamasters form">
<?php echo $this->Form->create('Npamaster');?>
	<fieldset>
		<legend><?php echo __('Add Npamaster'); ?></legend>
	<?php
		echo $this->Form->input('state');
		echo $this->Form->input('npa');
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