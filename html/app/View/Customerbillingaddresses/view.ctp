<div class="customerbillingaddressmasters view">
<h2><?php  echo __('Customerbillingaddressmaster');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Customer'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($customerbillingaddressmaster['Customer']['customerid'], array('controller' => 'customermasters', 'action' => 'view', $customerbillingaddressmaster['Customer']['customerid'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['address1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['address2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('City'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['city']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Stateorprov'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['stateorprov']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Country'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['country']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Zipcode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['zipcode']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Rowid'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customerbillingaddressmaster['Customerbillingaddressmaster']['rowid']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Customerbillingaddressmaster', true), array('action' => 'edit', $customerbillingaddressmaster['Customerbillingaddressmaster']['customerid'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Customerbillingaddressmaster', true), array('action' => 'delete', $customerbillingaddressmaster['Customerbillingaddressmaster']['customerid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $customerbillingaddressmaster['Customerbillingaddressmaster']['customerid'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Customerbillingaddressmasters', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Customerbillingaddressmaster', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Customermasters', true), array('controller' => 'customermasters', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Customer', true), array('controller' => 'customermasters', 'action' => 'add')); ?> </li>
	</ul>
</div>
