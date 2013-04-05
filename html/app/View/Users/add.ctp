<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Add User'); ?></legend>
    <?php
		$admin = array('admin');
		$options = array_merge($admin, $customers);
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('confirm', array('label' => 'Confirm Password', 'type' => 'password'));
        echo $this->Form->input('userrole', array(
			'options' => $options,
			'type' => 'select',
				'label' => 'Role'
        ));
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