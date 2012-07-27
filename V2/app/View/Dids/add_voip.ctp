
<div class="didmasters index">
<div class="Didlist" id="Didlist">

</div>
</div>
<div class="actions">
<?php echo $this->Html->script('jquery-1.7.2');?>
<?php echo $this->Html->script('jquery/didmasters_add_voip');?>
<?php echo $this->Form->create(null, array('default' => false)); ?>

	<h3><?php echo __('Actions'); ?></h3>
	<ul>
	<li><?php echo $this->Html->link(__('Back', true), array('action' => 'index'));?></li>
	</ul>
<?php	echo $this->Form->input('state',array(
									'class'=>'stateDropdown',
									'id'=>'stateDropdown',
									'type' => 'select',
									'empty' => '-- Select a State --',
									'onChange' => 'return false;',
									'options' => array(
										'AL' => 'AL',
										'AK' => 'AK',
										'AZ' => 'AZ',
										'AR' => 'AR',
										'CA' => 'CA',
										'CO' => 'CO',
										'CT' => 'CT',
										'DE' => 'DE',
										'DC' => 'DC',
										'FL' => 'FL',
										'HI' => 'HI',
										'ID' => 'ID',
										'IL' => 'IL',
										'IN' => 'IN',
										'IA' => 'IA',
										'KS' => 'KS',
										'KY' => 'KY',
										'LA' => 'LA',
										'ME' => 'ME',
										'MD' => 'MD',
										'MA' => 'MA',
										'MI' => 'MI',
										'MN' => 'MN',
										'MS' => 'MS',
										'MO' => 'MO',
										'MT' => 'MT',
										'NE' => 'NE',
										'NV' => 'NV',
										'NH' => 'NH',
										'NJ' => 'NJ',
										'NM' => 'NM',
										'NY' => 'NY',
										'NC' => 'NC',
										'ND' => 'ND',
										'OH' => 'OH',
										'OK' => 'OK',
										'PA' => 'PA',
										'RI' => 'RI',
										'SC' => 'SC',
										'SD' => 'SD',
										'TN' => 'TN',
										'TX' => 'TX',
										'UT' => 'UT',
										'VT' => 'VT',
										'VA' => 'VA',
										'WA' => 'WA',
										'WV' => 'WV',
										'WI' => 'WI',
										'WY' => 'WY',
										'AS' => 'AS',
										'GU' => 'GU',
										'MP' => 'MP',
										'PR' => 'PR',
										'VI' => 'VI'
									)
								)
							);
?>
<?php	echo $this->Form->input('ratecenter', array(
										'class'=>'ratecenterDropdown',
										'id'=>'ratecenter',
										'onChange' => 'return false;',
										'type' => 'select',
										'empty' => 'All'
									)
								); 
?>
<?php	echo $this->Form->input('tier', array(
										'class'=>'tierDropdown',
										'id'=>'tier',
										'onChange' => 'return false;',
										'type' => 'select',
										'empty' => 'All'
									)
								); 
?>
<?php	echo $this->Form->input('lata', array(
										'class'=>'lataDropdown',
										'id'=>'lata',
										'onChange' => 'return false;',
										'type' => 'select',
										'empty' => 'All'
									)
								); 
?>
<?php echo $this->Form->end(array(
		'label' => 'View DIDs',
		'class'=>'viewdidbutton',
		'onClick' => 'return false;'
	)
); ?>
</div>

