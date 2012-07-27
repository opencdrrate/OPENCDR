<div class="reports index">
<ul>
<li><?php echo $this->Html->link('Calls by Customer and Type', array('controller'=>'Callsbycustomerandtype'));?></li>
<li><?php echo $this->Html->link('Calls by Customer and Type per month', array('controller'=>'Callsbycustomerandtypepermonth'));?></li>
<li><?php echo $this->Html->link('Inbound vs. Outbound minutes', array('controller'=>'Inboundvsoutboundreport'));?></li>
<li><?php echo $this->Html->link('Calls Per Month, Carrier', array('controller'=>'Callspermonthpercarrier'));?></li>
<li><?php echo $this->Html->link('Average Monthly Inbound Calls Per Carrier', array('controller'=>'avgmonthlyinboundcalls'));?></li>
<li><?php echo $this->Html->link('Average Monthly Outbound Calls Per Carrier', array('controller'=>'avgmonthlyoutboundcalls'));?></li>
<li><?php echo $this->Html->link('Average Monthly Inbound Calls Per Carrier, rate center', array('controller'=>'avgmonthlyinboundcallsratecenter'));?></li>
<li><?php echo $this->Html->link('Average Monthly Outbound Calls Per Carrier, rate center', array('controller'=>'avgmonthlyoutboundcallsratecenter'));?></li>
<li><?php echo $this->Html->link('Concurrent calls', array('controller'=>'concurrentcalls'));?></li>
<li><?php echo $this->Html->link('Concurrent calls : Peak and Average per day', array('controller'=>'concurrentcallspeak'));?></li>
<li><?php echo $this->Html->link('Concurrent calls : Inbound vs Outbound Peak and Average per day', array('controller'=>'concurrentcallsinvsout'));?></li>
<li><?php echo $this->Html->link('Concurrent calls : Inbound vs Outbound Peak and Average per day, rate center', array('controller'=>'Concurrentcallsratecenterreport'));?></li>
<li><?php echo $this->Html->link('Concurrent calls : Inbound vs Outbound Peak and Average per day, carrier', array('controller'=>'Concurrentcallscarrierreport'));?></li>
<li><?php echo $this->Html->link('Outgoing calls per DID', array('controller'=>'Callsperdidoutgoing'));?></li>
<li><?php echo $this->Html->link('Incoming calls per DID', array('controller'=>'Callsperdidincoming'));?></li>
<li><?php echo $this->Html->link('Toll-Free calls per DID', array('controller'=>'Callsperdidtollfree'));?></li>
<li><?php echo $this->Html->link('Calls by customer, type', array('controller'=>'Callspercustomerandtype'));?></li>
<li><?php echo $this->Html->link('Calls by IP address', array('controller'=>'Callsperipaddress'));?></li>
</ul>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Back', true), '/'); ?></li>
	</ul>
</div>

