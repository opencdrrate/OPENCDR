<div class="tieredoriginationratecentermasters form">
<?php echo $this->Form->create(''); ?>
	<fieldset>
		<legend><?php echo __('Edit Rate Center'); ?></legend>
		<label>Rate Center</label><div class="readonly"><?php echo $this->data['Tieredoriginationratecentermaster']['ratecenter'];?></div>
	<?php
		echo $this->Form->input('ratecenter',array('type' =>'hidden'));
		echo $this->Form->input('tier',array('label' => 'Tier', 'style'=> 'width:400px;'));
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