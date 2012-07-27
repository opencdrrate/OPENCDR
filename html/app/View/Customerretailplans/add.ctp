<div class="customerretailplanmasters form">
<?php echo $this->Form->create('');?>
	<fieldset>
		<legend><?php echo __('Assign Retail Plan'); ?></legend>
		<p>
Only customers with a type of retail will be listed on this page. 
		</p>
	<?php
		echo $this->Form->input('customerid', array(
			'options' => $customers,
			'empty' => '-- Please Select a Customer --',
			'label' => 'CustomerID'
			)
		);
		echo $this->Form->input('planid', array(
				'options' => $retailplans,
				'empty' => '-- Please Select a Retail Plan --',
				'label'=>'PlanID'
			)
		);
		echo $this->Form->input('activationdate', array('label'=>'Activation Date'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Back', true), array('controller'=>'Retailplans','action' => 'index'));?></li>
	</ul>
</div>