<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo 'Please enter a new password'; ?></legend>
    <?php
        echo $this->Form->input('password', array('label'=>'New Password'));
        echo $this->Form->input('confirm', array('label'=>'Confirm New Password', 'type'=>'password'));
		echo $this->Form->input('id');
		echo $this->Form->input('username', array('type'=>'hidden'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back', array('controller'=>'Users', 'action' => 'index'));?></li>
	</ul>
</div>