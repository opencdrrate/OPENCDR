<!-- app/View/Users/add.ctp -->
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Create New User for ' . $customerid); ?></legend>
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('confirm', array('label' => 'Confirm Password', 'type' => 'password'));
        echo $this->Form->input('role', array(
			'value' => 'customer',
			'type' => 'hidden'
        ));
		echo $this->Form->input('customerid', array(
			'value' => $customerid,
			'type' => 'hidden'
        ));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link('Back', array('controller'=>'Customers', 'action' => 'view', $customerid));?></li>
	</ul>
</div>