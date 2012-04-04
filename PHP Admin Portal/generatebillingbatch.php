<?php
include 'config.php';
include 'lib/Page.php'; 

function CreateDropDown($connectString){

$query = 'select distinct(billingcycle) from customermaster order by billingcycle;';
$db = pg_connect($connectString);
$result = pg_query($db, $query);
if (!$result) {
	echo pg_last_error();
	exit();
}

$dropdown = '<select name="billingcycleid">';
$dropdown .= '<option></option>';

while($myrow = pg_fetch_assoc($result)) { 
	$dropdown .= '<option value="'.$myrow['billingcycle'].'">'.$myrow['billingcycle'].'</option>';
}

$dropdown .= '</select>';

return $dropdown;
}
if(isset($_POST["submit"])){
	$connectString = $_POST["connectString"];
	$batchid = $_POST["batchid"];
	$billingduedate = isset($_REQUEST["billingduedate"]) ? $_REQUEST["billingduedate"] : "";
	$billingdate = isset($_REQUEST["billingdate"]) ? $_REQUEST["billingdate"] : "";
	$billingcycleid = $_POST["billingcycleid"];
	$usageperiodend = isset($_REQUEST["usagedateend"]) ? $_REQUEST["usagedateend"] : "";
	$recurringfeestart = isset($_REQUEST["recurrstart"]) ? $_REQUEST["recurrstart"] : "";
	$recurringfeeend = isset($_REQUEST["recurrend"]) ? $_REQUEST["recurrend"] : "";

	$sqlStatement = "select \"fnGenerateBillingBatch\"('$batchid', '$billingdate', '$billingduedate', '$billingcycleid',  '$usageperiodend', '$recurringfeestart', '$recurringfeeend');"; 

	$db = pg_connect($connectString);
	$result = pg_query($db, $sqlStatement);
	if (!$result) {
		echo pg_last_error();
		exit();
	}
	else {
		header('Location: listbillbatches.php');
	}
}
?>

<?php echo GetPageHead("Generate Billing Batch", "main.php")?>
</head>
   <div id="body"> 
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
<label>BatchID:</label><input name="batchid" type="text"/><br />
<label>Billing Date:</label>
<label>
	  <?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');
		require_once('lib/calendar/classes/tc_calendar.php');

		$myCalendar = new tc_calendar("billingdate", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>
<label>Billing Due Date:</label>
<label>
	  <?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');
		require_once('lib/calendar/classes/tc_calendar.php');

		$myCalendar = new tc_calendar("billingduedate", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>
<label>Billing CycleID:</label><?php echo CreateDropDown($connectstring); ?><br />
<label>End Usage Date:</label>
<label>
	  <?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');
		require_once('lib/calendar/classes/tc_calendar.php');

		$myCalendar = new tc_calendar("usagedateend", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>
<label>Recurring Fee Period Start:   </label>
<label>
	  <?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');
		require_once('lib/calendar/classes/tc_calendar.php');

		$myCalendar = new tc_calendar("recurrstart", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>

<label>Recurring Fee Period End:   </label>
<label>
	<?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');
		require_once('lib/calendar/classes/tc_calendar.php');

		$myCalendar = new tc_calendar("recurrend", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>
	<input name="generate" type="hidden" type="hidden"/>
	<input name="connectString" type="hidden" type="hidden" value="<?php echo $connectstring;?>"/>
	<input type="submit" name="submit" value="Generate Billing Batch"/>
</form>
    </div>
   
<?php echo GetPageFoot("","");?>