<div class="billingbatchmasters form">
<?php echo $this->Form->create(false, array('action'=>'add'));?>
	<fieldset>
		<legend><?php echo __('Add Billing Batch'); ?></legend>
	<?php
		echo $this->Form->input('Billingbatchmaster.billingbatchid', array('label'=>'Billing BatchID', 'type'=>'text', 'style' => 'width:400px;'));
		echo $this->Form->input('Billingbatchmaster.billingdate', array('type' => 'date', 'label'=>'Billing Date'));
		echo $this->Form->input('Billingbatchmaster.duedate', array('type' => 'date', 'label' => 'Due Date'));
		echo $this->Form->input(
			'Billingbatchmaster.billingcycleid',
			array(
				'options' => $Billingcycles,
				'type' => 'select',
				'empty' => '-- Select a billing cycle --',
				'label' => 'Billing CycleID',
				'value'=>''
			)
		);
		echo $this->Form->input('Billingbatchmaster.usageperiodend', array('type' => 'date', 
				'label'=>'Usage Period End Date
				<div id="tooltip1"><a>What\'s this?
							<span>
								All CDR up to and including this date will be part of this bill run.
							</span>
						</a>
				</div>
		'));
		echo $this->Form->input('Billingbatchmaster.recurringfeestart', array('type' => 'date', 'label'=>'Recurring Fee Start Date'));
		echo $this->Form->input('Billingbatchmaster.recurringfeeend', array('type' => 'date', 'label' => 'Recurring Fee End Date'));
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