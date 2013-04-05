
<div class="users form">
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Admin setup'); ?></legend>
<p>No admin detected.  Please provide a username and password for your administrator.</p>
    <?php
        echo $this->Form->input('username');
        echo $this->Form->input('password');
        echo $this->Form->input('confirm', array('label' => 'Confirm Password', 'type' => 'password'));
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>