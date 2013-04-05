<div class="interstateratemasters form">
<?php echo $this->Form->create('Simpleterminationrates', array('enctype' => 'multipart/form-data', 'action'=>'import') );?>
	<fieldset>
		<legend><?php echo __('Upload Simple Termination Rate file'); ?></legend>
		<?php echo $this->Form->input('customerid', array('type'=>'hidden', 'value' => $customerid)); ?>
		<label>Choose a file:</label><?php echo $this->Form->file('Document.filename', array('multiple'=>'multiple'));?>
		<?php echo $this->Form->end(__('Submit', true));?>
	</fieldset>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index',$customerid));?></li>
	</ul>
</div>