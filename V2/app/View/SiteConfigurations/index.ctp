<div class="settings index">
	
<?php echo $this->Form->create('SiteConfigurations', array('controller' =>'SiteConfigurations' ,'action' => 'save'));?>
	<fieldset>
		<legend><?php echo __('Settings'); ?></legend>
	<?php echo $this->Form->input('sitename', array('label' => 'Site Name'));?>
	<?php echo $this->Form->input(
			'currency',
			array(
				'options' => array(
							'USD' => 'Dollar',
							'EUR' => 'Euro'
							),
				'type' => 'select',
				'empty' => '-- Select a Currency --',
				'label' => 'Currency'
			)
		);?>
	</fieldset>
	<fieldset>
		<legend><?php echo __('Company Information'); ?></legend>
	<?php echo $this->Form->input('companyname', array('label' => 'Company Name')); ?>
	<?php echo $this->Form->input('address1', array('label' => 'Company Address 1')); ?>
	<?php echo $this->Form->input('address2', array('label' => 'Company Address 2')); ?>
	<?php echo $this->Form->input('city', array('label' => 'Company City')); ?>
	<?php echo $this->Form->input('state', array('label' => 'Company State')); ?>
	<?php echo $this->Form->input('postal', array('label' => 'Company Zip/Postal')); ?>
	<?php echo $this->Form->input('country', array('label' => 'Company Country')); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __('VoIP Innovations Credentials'); ?></legend>
	<?php echo $this->Form->input('voip_user', array('label' => 'VoIP Innovations API Username')); ?>
	<?php echo $this->Form->input('voip_pwd', array('label' => 'VoIP Innovations API Password', 'type' => 'password')); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __('Vitelity Credentials'); ?></legend>
	<?php echo $this->Form->input('vitelity_username', array('label' => 'Vitelity API Username')); ?>
	<?php echo $this->Form->input('vitelity_password', array('label' => 'Vitelity API Password')); ?>
	</fieldset>
	<fieldset>
		<legend><?php echo __('SMTP Settings'); ?></legend>
	<?php echo $this->Form->input('smtp_host', array('label' => 'Host')); ?>
	<?php echo $this->Form->input('smtp_port', array('label' => 'Port')); ?>
	<?php echo $this->Form->input('smtp_user', array('label' => 'Username')); ?>
	<?php echo $this->Form->input('smtp_password', array('label' => 'Password', 'type'=>'password')); ?>
	</fieldset>
<?php echo $this->Form->end(__('Save Settings', true));?>

</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
<ul>
	<li>
		<?php echo $this->Html->link(__('Back', true), '/'); ?>
	</li>
</ul>
</div>