<div class="retailplanmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Edit Retail Plan'); ?></legend>
	<?php
		echo $this->Form->input('plandescription', array('label'=>'Description'));
		echo $this->Form->input('originationrate', array('label'=>'Origination Rate'));
		echo $this->Form->input('tollfreeoriginationrate', array('label'=>'Toll-Free Origination Rate'));
		echo $this->Form->input('freeminutespercycle', array('label'=>'Free Minutes Per Cycle'));
		echo $this->Form->input('rowid', array('type'=>'hidden'));
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