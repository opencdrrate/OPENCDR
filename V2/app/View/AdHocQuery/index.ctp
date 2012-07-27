<div class="adhocquery form">
<?php echo $this->Form->create('AdHocQuery', array('controller'=>'AdHocQuery','action'=>'query', 'url'=>'/AdHocQuery/query'));?>
	<fieldset>
		<legend><?php echo __('Adhoc Query'); ?></legend>
		<?php echo $this->Form->input('Query', array('type' => 'textarea'));?>
	</fieldset>
<?php echo $this->Form->end(__('Submit Query'));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back', true), '/');?></li>
	</ul>
</div>