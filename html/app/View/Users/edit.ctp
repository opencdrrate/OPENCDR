<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo 'Edit: ' . $this->data['User']['username']; ?></legend>
    <?php
        echo $this->Form->input('role', array(
            'options' => array('admin' => 'Admin', 'customer' => 'Customer')
        ));
		
		echo $this->Form->input('id');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Change Password', array('controller'=>'Users', 'action' => 'newpwd', $this->data['User']['id']));?></li>
		<li><?php echo $this->Html->link('Back', array('controller'=>'Users', 'action' => 'index'));?></li>
	</ul>
</div>