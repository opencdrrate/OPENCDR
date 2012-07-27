<div class="customermasters form">
<?php echo $this->Form->create('Customer');?>
	<fieldset>
		<legend><?php echo __('Edit Customer'); ?></legend>
	<?php
		echo $this->Form->input('customerid', array('label' => 'CustomerID'));
		echo $this->Form->input('customername', array('label' =>'Customer Name'));
		echo $this->Form->input('lrndiprate', array('label' =>'LRN Dip Rate','style' =>'width:400px'));
		echo $this->Form->input('cnamdiprate', array('label' =>'CNAM Dip Rate','style' =>'width:400px'));
		echo $this->Form->input('indeterminatejurisdictioncalltype', 
			array(
				'label'=>'Indeterminate Jurisdiction Call Type',
				'options' => array(
					'5'=>'Intrastate',
					'10'=>'Interstate'
				)
			)
		);
		echo $this->Form->input('billingcycle', array('label' =>'Billing Cycle','style' =>'width:400px'));
		echo $this->Form->input('customertype', 
			array(
				'label' =>'Customer Type',
				'options' => array(
					'Wholesale' => 'wholesale',
					'Retail' => 'retail'
				)
			)
		);
		echo $this->Form->input('creditlimit', array('label' => 'Credit Limit','style' =>'width:400px'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>

	<fieldset>
		<legend><?php echo __('Edit SIP Credentials'); ?></legend>
		<?php
			echo $this->Form->create('Sipcredential', array('controller' => 'Sipcredential', 'action' => 'edit'));
			echo $this->Form->input('customerid', array('type'=>'hidden', 'value' => $this->data['Customer']['customerid']));
			echo $this->Form->input('sipusername', 
				array(
					'label' => 'SIP Username', 
					'style' =>'width:400px'
				)
			);
			echo $this->Form->input('sippassword', 
				array(
					'label' => 'SIP Password', 
					'type' => 'password',
					'style' =>'width:400px'
				)
			);
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