<?php
$currency = $siteconfiguration['currency'];
$currencySettings = $siteconfiguration['currencysettings'];
?>
<div class="customers view">
<h2><?php  echo __('Customer');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('CustomerID'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['customerid']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Customer Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['customername']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('LRN Dip Rate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['lrndiprate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('CNAM Dip Rate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['cnamdiprate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Indeterminate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['indeterminatejurisdictioncalltype']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Billing Cycle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['billingcycle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Customer Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['customertype']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Credit Limit'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $customer['Customer']['creditlimit']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back to Customers', true), array('action' => 'index')); ?> </li>
	</ul>
</div>
	<?php if ($customer['Customer']['customertype'] == 'Retail'):?>
	
	<div class="view">
		<h3><?php echo __('Retail Plan');?></h3>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Activationdate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['RetailPlan']['activationdate'];?>
&nbsp;</dd>
		</dl>
	</div>
	<?php endif; ?>
		<div class="view">
		<h3><?php echo __('Billing Address');?></h3>
	<?php if (!empty($customer['BillingAddress'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address1');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['address1'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address2');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['address2'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('City');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['city'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('State / Province');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['stateorprov'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Country');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['country'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Zip / Postal Code');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['BillingAddress']['zipcode'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
		<?php if (!empty($customer['BillingAddress']['customerid'])):?>
				<li><?php echo $this->Html->link(__('Edit Billing Address', true), array('controller' => 'customerbillingaddresses', 'action' => 'edit', $customer['BillingAddress']['customerid'])); ?></li>		
		<?php endif; ?>
		<?php if (empty($customer['BillingAddress']['customerid'])):?>
				<li><?php echo $this->Html->link(__('Add Billing Address', true), array('controller' => 'customerbillingaddresses', 'action' => 'add', $customer['Customer']['customerid'])); ?></li>
		<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<div class="view">
		<h3><?php echo __('Contact Information');?></h3>
	<?php if (!empty($customer['ContactInformation'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Email');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['ContactInformation']['primaryemailaddress'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
		<?php if (!empty($customer['ContactInformation']['customerid'])):?>
				<li><?php echo $this->Html->link(__('Edit Contact Info', true), array('controller' => 'customercontactinformations', 'action' => 'edit', $customer['ContactInformation']['customerid'])); ?></li>
		<?php endif; ?>
		<?php if (empty($customer['ContactInformation']['customerid'])):?>
			<li><?php echo $this->Html->link(__('Add Contact Info', true), array('controller' => 'customercontactinformations', 'action' => 'add', $customer['Customer']['customerid'])); ?></li>
		<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<div class="view">
		<h3><?php echo __('SIP Credentials');?>
				<div id="tooltip1"><a>What's this?
				<span>
					Use this to store customer-level SIP credentials. 
					You also have the option to use DID-level SIP credentials.
				</span></a></div>
		</h3>
	<?php if (!empty($customer['Sipcredential'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('User Name');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $customer['Sipcredential']['sipusername'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
		<?php if (!empty($customer['Sipcredential']['customerid'])):?>
				<li><?php echo $this->Html->link(__('Edit Credentials', true), array('controller' => 'sipcredentials', 'action' => 'edit', $customer['Sipcredential']['customerid'])); ?></li>
		<?php endif; ?>
		<?php if (empty($customer['Sipcredential']['customerid'])):?>
			<li><?php echo $this->Html->link(__('Add Credentials', true), array('controller' => 'sipcredentials', 'action' => 'add', $customer['Customer']['customerid'])); ?></li>
		<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<div class="view">
	<h3><?php echo __('Payment Items');?></h3>
	<?php if (!empty($customer['Payment'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Customerid'); ?></th>
		<th><?php echo __('Paymentdate'); ?></th>
		<th><?php echo __('Paymentamount'); ?></th>
		<th><?php echo __('Paymenttype'); ?></th>
		<th><?php echo __('Paymentnote'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($customer['Payment'] as $payment):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $payment['customerid'];?></td>
			<td><?php echo $payment['paymentdate'];?></td>
			<td><?php echo $this->Number->currency($payment['paymentamount'], $currency,$currencySettings);?></td>
			<td><?php echo $payment['paymenttype'];?></td>
			<td><?php echo $payment['paymentnote'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'payments', 'action' => 'edit', $payment['rowid'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'payments', 'action' => 'delete', $payment['rowid']), null, sprintf(__('Are you sure you want to delete # %s?', true), $payment['rowid'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Payment', true), array('controller' => 'payments', 'action' => 'add', $customer['Customer']['customerid']));?> </li>
		</ul>
	</div>
</div>
