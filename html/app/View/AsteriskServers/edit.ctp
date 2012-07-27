<div class="asteriskservers form">
<?php echo $this->Form->create('AsteriskServer', array('action' => 'edit'));?>
	<fieldset>
		<legend><?php echo __('Edit Asterisk Server'); ?></legend>
		
		<?php echo $this->Form->input('servername',array('label' => 'Server Name'));?>
		<?php echo $this->Form->input('serveripordns',array('label' => 'IP Address or DNS'));?>
		<?php echo $this->Form->input('mysqlport',array('label' => 'MySQL Port'));?>
		<?php echo $this->Form->input('mysqllogin',array('label' => 'MySQL Login'));?>
		<?php echo $this->Form->input('mysqlpassword',array('label' => 'MySQL Password', 'type'=>'password'));?>
		<?php echo $this->Form->input('cdrdatabase',array('label' => 'MySQL Database'));?>
		<?php echo $this->Form->input('cdrtable',array('label' => 'MySQL Table'));?>
		<?php echo $this->Form->input('active',array('label' => 'Active',
				'options' => array(
					'1' => 'Yes',
					'0' => 'No'
				)));?>
		<?php echo $this->Form->input('rowid');?>
	</fieldset>

<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back', array('action'=>'index'));?></li>
	</ul>
</div>