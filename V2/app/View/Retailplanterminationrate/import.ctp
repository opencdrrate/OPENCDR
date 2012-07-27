<div class="retailplanterminationrate form">
<?php echo $this->Form->create('', array('enctype' => 'multipart/form-data') );?>
	<fieldset>
		<legend><?php echo __('Upload Retail Termination Rate File'); ?></legend>
		<?php echo $this->Form->input('planid', array('type'=>'hidden', 'value' => $planid)); ?>
		<label>Choose a file:</label><?php echo $this->Form->file('Document.filename', array('multiple'=>'multiple'));?>
		<?php echo $this->Form->end(__('Submit', true));?>
	</fieldset>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index',$planid));?></li>
	</ul>
</div>