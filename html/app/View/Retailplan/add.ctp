<div class="retailplanmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Create New Retail Plan'); ?></legend>
	<?php
		echo $this->Form->input('planid', array('label'=>'PlanID', 'type' => 'text'));
		echo $this->Form->input('plandescription', array('label'=>'Description'));
		echo $this->Form->input('servicefee', array('label'=>'Service Fee', 'default'=>0));
		echo $this->Form->input('originationrate', array('label'=>'Origination Rate', 'default' => '0.00000'));
		echo $this->Form->input('tollfreeoriginationrate', array('label'=>'Toll-Free Origination Rate', 'default' => '0.00000'));
		echo $this->Form->input('freeminutespercycle', array('label'=>'Free Minutes Per Cycle', 'default' => '0'));
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