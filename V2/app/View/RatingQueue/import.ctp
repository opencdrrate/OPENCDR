<div class="callrecordmasterTbrs form">
<?php echo $this->Form->create('', array('enctype' => 'multipart/form-data') );?>
	<fieldset>
		<legend><?php echo __('Upload CDR File'); ?></legend>
		<label>Select a file type : </label><?php
		$options = array('bandwidth' => 'Bandwidth', 
						'asterisk' => 'Asterisk',
						'itel' => 'iTel',
						'vitelity' => 'Vitelity',
						'thinktel' => 'ThinkTel',
						'aretta' => 'Aretta',
						'voip' => 'VOIP Innovations',
						'cisco' => 'Cisco',
						'slinger' => 'Slinger',
						'telepo' => 'Telepo',
						'nextone' => 'Nextone',
						'telastic' => 'Telastic',
						'netsapiens' => 'NetSapiens',
						'sansay' => 'Sansay');
						
		echo $this->Form->select('type', $options)?>
		<label>Choose a file:</label><?php echo $this->Form->file('Document.filename', array('multiple'=>'multiple'));?>
		<?php echo $this->Form->end(__('Submit', true));?>

	</fieldset>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back', array('action'=>'index'));?></li>
	</ul>
</div>