<div class="didmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Add DID'); ?></legend>
	<?php
		echo $this->Form->input(
			'customerid',
			array(
				'options' => $customers,
				'type' => 'select',
				'empty' => '-- Select a Customer --',
				'label' => 'Customer'
			)
		);?>
		
		<?php echo $this->Form->input('did', array(
											'type' => 'text',
											'label'=>'DID',
											'style' => 'width:400px;'
										));?>
		<p>DIDs should be entered in E.164 format. Example: +16138221234</p>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index'));?></li>
	</ul>
</div>