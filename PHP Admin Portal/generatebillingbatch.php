<?php
	include_once 'config.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path.'lib/calendar/classes/tc_calendar.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();	
include_once $path . 'lib/Page.php'; 

function CreateDropDown($connectstring){

$query = 'select distinct(billingcycle) from customermaster order by billingcycle;';
$db = pg_connect($connectstring);
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
	$connectstring = $_POST["connectstring"];
	$batchid = $_POST["batchid"];
	$billingduedate = isset($_REQUEST["billingduedate"]) ? $_REQUEST["billingduedate"] : "";
	$billingdate = isset($_REQUEST["billingdate"]) ? $_REQUEST["billingdate"] : "";
	$billingcycleid = $_POST["billingcycleid"];
	$usageperiodend = isset($_REQUEST["usagedateend"]) ? $_REQUEST["usagedateend"] : "";
	$recurringfeestart = isset($_REQUEST["recurrstart"]) ? $_REQUEST["recurrstart"] : "";
	$recurringfeeend = isset($_REQUEST["recurrend"]) ? $_REQUEST["recurrend"] : "";
	
	$sqlStatement = "select \"fnGenerateBillingBatch\"('$batchid', '$billingdate', '$billingduedate', '$billingcycleid',  '$usageperiodend', '$recurringfeestart', '$recurringfeeend');"; 

	$db = pg_connect($connectstring);
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
		
		echo ('<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>');
		

		$myCalendar = new tc_calendar("billingdate", true, false);
	  	$image = $path."lib/calendar/images/iconCalendar.gif";
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("/Shared/lib/calendar/");
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
		
		echo ('<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>');

		$myCalendar = new tc_calendar("billingduedate", true, false);
	  	$image = $path."lib/calendar/images/iconCalendar.gif";
	  	$myCalendar->setIcon($image);	
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("/Shared/lib/calendar/");
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
		
		echo ('<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>');

		$myCalendar = new tc_calendar("usagedateend", true, false);
	  	$image = $path."lib/calendar/images/iconCalendar.gif";
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("/Shared/lib/calendar/");
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
		
		echo ('<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>');

		$myCalendar = new tc_calendar("recurrstart", true, false);
	  	$image = $path."lib/calendar/images/iconCalendar.gif";
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("/Shared/lib/calendar/");
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
		
		echo ('<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>');

		$myCalendar = new tc_calendar("recurrend", true, false);
		$image = $path."lib/calendar/images/iconCalendar.gif";
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("/Shared/lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?>
</label><br/><br/>
	<input name="generate" type="hidden" type="hidden"/>
	<input name="connectstring" type="hidden" type="hidden" value="<?php echo $connectstring;?>"/>
	<input type="submit" name="submit" value="Generate Billing Batch"/>
</form>
    </div>
   
<?php echo GetPageFoot("","");?>