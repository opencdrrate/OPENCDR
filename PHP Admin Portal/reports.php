<?php 

include 'lib/Page.php';
echo GetPageHead("Reports", "main.php");
?>

<div id="body">
<p><font color="red">Calls must be successfully rated before they can be reported on.</font></p>
<br/>

<a href="rep_callsbycustomerandtype.php">Calls by Customer and Type</a></br>
<a href="rep_callsbycustomerandtypepermonth.php">Calls by Customer and Type (per Month)</a></br>
<a href="rep_inboundoutboundminutescustomercarriermonth.php">Inbound vs. Outbound Minutes Per Customer, Carrier, Month</a></br>
<a href="rep_callspermonthcarrier.php">Calls Per Month, Carrier</a></br>
<a href="rep_avgmonthlyinboundcallspercarrier.php">Average Monthly Inbound Calls Per Carrier</a></br>

<a href="rep_avgmonthlyoutboundcallspercarrier.php">Average Monthly Outbound Calls Per Carrier</a></br>

<a href="rep_avgmonthlyinboundcallspercarrierratecenter.php">Average Monthly Inbound Calls Per Carrier, RateCenter</a></br>

<a href="rep_avgmonthlyoutboundcallspercarrierratecenter.php">Average Monthly Outbound Calls Per Carrier, RateCenter</a></br>

<a href="rep_concurrentcalls.php">Concurrent Calls</a></br>

<a href="rep_concurrentcallspeakandaverageperday.php">Concurrent Calls - Peak and Average per Day</a></br>

<a href="rep_concurrentcallsinvoutpeakavgperday.php">Concurrent Calls - Inbound vs. Outbound Peak and Average per Day</a></br>

<a href="rep_concurrentcallsinvoutpeakavgperdayratecenter.php">Concurrent Calls - Inbound vs. Outbound Peak and Average per Day, RateCenter</a></br>

<a href="rep_concurrentcallsinvoutpeakavgperdaycarrier.php">Concurrent Calls - Inbound vs. Outbound Peak and Average per Day, Carrier</a></br>

<a href="rep_callsperdidoutgoing.php">Calls per DID - Outgoing</a></br>

<a href="rep_callsperdidincoming.php">Calls per DID - Incoming</a></br>

<a href="rep_callsperdidtollfree.php">Calls per DID - Toll-free</a></br>

<a href="rep_callspercustomerandtype.php">Calls per Customer and Type</a></br>

<a href="rep_callsperipaddress.php">Calls per IP Address</a></br>

</div>
<?php echo GetPageFoot("","");?>

