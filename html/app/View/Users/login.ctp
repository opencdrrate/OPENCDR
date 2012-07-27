<div class="users form">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User', array('inputDefaults'=>array('div'=> false))); ?>
    <fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
    <?php
        echo '<div class="input text">'. $this->Form->input('username', array('style'=>'width:400px;')) . '</div>';
        echo '<div class="input text">'. $this->Form->input('password', array('style'=>'width:400px;')) . '</div>';
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>
</div>