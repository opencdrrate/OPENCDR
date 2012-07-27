<div class="customermasters form">
<?php echo $this->Form->create('Customer', array('action' => 'add'));?>
	<fieldset>
		<legend><?php echo __('Add Customer'); ?></legend>
	<?php
		echo $this->form->input('Customer.customerid', 
							array(
								'type' => 'text',
								'style' =>'width:400px',
								'label' => 'CustomerID'
							)
						);
	?>

	<?php
		echo $this->Form->input('customername',array('label' => 'Customer Name'));
		echo $this->Form->input('ContactInformation.primaryemailaddress',array('label' => 'Primary Email Address'));
		echo $this->Form->input('lrndiprate',
									array(
										'label' => 'LRN Dip Rate
											<div id="tooltip1"><a>What\'s this?
											<span>
												This is the fee to charge for each call that was dipped for 
												Local Routing Number.
											</span></a></div>',
										'style' =>'width:400px',
										'default' => '0.00000'
									)
								);
		echo $this->Form->input('cnamdiprate',
									array(
										'label' => 'CNAM Dip Rate
										<div id="tooltip1"><a>What\'s this?
										<span>
											This is the fee to charge for each call that was dipped for CNAM.
										</span></a></div>',
										'style' =>'width:400px',
										'default' => '0.00000'
									)
								);
		echo $this->Form->input('indeterminatejurisdictioncalltype', 
			array(
				'label' => 'Indeterminate Jurisdiction Call Type',
				'options' => array(
					'5'=>'Intrastate',
					'10'=>'Interstate'
				)
			)
		);
	?>
	<?php
		echo $this->Form->input('billingcycle', array(
			'label' => 'Billing Cycle
				<div id="tooltip1"><a>What\'s this?
				<span>
					For monthly customers, put the day of the month you want them billed. 
					For weekly customers, put the day of the week. For prepaid customers, use the 
					word \'prepaid\'.
				</span></a></div>',
			'style' =>'width:400px')
		);
	?>
		
	<?php
		echo $this->Form->input('customertype', 
			array(
				'label' => 'Customer Type',
				'options' => array(
					'Wholesale' => 'wholesale',
					'Retail' => 'retail'
				)
			)
		);
		echo $this->Form->input('creditlimit', array(
			'label' => 'Credit Limit
			<div id="tooltip1"><a >What\'s this?<span>
			Use this feature to control how much postpaid customers can 
	spend before they need to make a payment.</span></a></div>',
			'style' =>'width:400px')
		);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back', array('action'=>'index'));?></li>
		<li><?php echo $this->Html->link('HELP', 'https://sourceforge.net/p/opencdrrate/home/CustomerMaster/', array('target'=>'_blank'));?></li>
	</ul>
</div>