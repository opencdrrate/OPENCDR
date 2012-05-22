<?php
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';

	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once($path . 'lib/calendar/classes/tc_calendar.php');
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

	define('ROOT_PATH',$_SERVER['DOCUMENT_ROOT']);

	ini_set('include_path', '/var/www/html/');
	
 	if (isset($_POST['submit'])) {	

		$db = pg_connect($connectstring);
        	if (!$db) {
         	die("Error in connection: " . pg_last_error());
     		}

		$good = 0;

		function BadEntry($errormessage) {
	
			echo ("<script type='text/javascript'>");
			echo ("alert('$errormessage');");
			echo ("</script>");
		
			#exit();
			return 1;
		}
    
     		$customerid = pg_escape_string($_POST['customerid']);
     		$unitamount = pg_escape_string($_POST['unitamount']);
     		$quantity = pg_escape_string($_POST['quantity']);
		$chargedate = isset($_REQUEST["date5"]) ? $_REQUEST["date5"] : "";
		$chargedesc = pg_escape_string($_POST['chargedesc']);

		if ($customerid == "") {
	
			$good = BadEntry("CustomerID is required");
     		}

		else if ($chargedesc == "") {
	
			$good = BadEntry("Charge Description is required");
     		}

     		else if ($unitamount == "") {

			$good = BadEntry("Unit Amount is required");
     		}

		else if (!is_numeric($unitamount)) {

			$good = BadEntry("Unit Amount must be numeric");
		}

     		else if ($quantity == "") {

			$good = BadEntry("Quantity is required");
     		}

		else if (!is_numeric($quantity)) {

			$good = BadEntry("Quantity must be numeric");
		}

		if ($good == 0) {

                                      
     			$sql = "INSERT INTO onetimechargequeue (customerid, chargedate, unitamount, quantity, chargedesc) VALUES ('$customerid', '$chargedate', '$unitamount', '$quantity', '$chargedesc')";
     			$result = pg_query($db, $sql);
	 
			if (!$result) {
         			die("Error in SQL query: " . pg_last_error());
     			}
    
        		pg_free_result($result);
    
     			pg_close($db);

			$url = "listcustomeronetimecharges.php?customerid=$customerid";

			echo ("<script type='text/javascript'>");
			echo ("window.location = '$url'");
			echo ("</script>");

		}

 	}
else {

	$customerid = $_GET['customerid'];
}

 ?> 
<?php echo GetPageHead("Add One-time Charge to $customerid", "rates.php")?>
</head>
   <div id="body">  
    <form name="customerform" id="standardform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <label>CustomerID:</label><input type="text" name="customerid" value="<?php echo $customerid; ?>" READONLY><br/>
      <label>Unit Amount:</label><input type="text" name="unitamount" value="<?php if (isset($_POST['submit'])) { echo $unitamount; } else { echo 0.00; } ?>" ><br/>
      <label>Quantity:</label><input type="text" name="quantity" value="<?php if (isset($_POST['submit'])) { echo $quantity; } else { echo 0.00; } ?>" ><br/>
      <label>Charge Description:</label><input type="text" name="chargedesc" value="<?php if (isset($_POST['submit'])) { echo $chargedesc; } ?>"><br/>
      <label>Charge Date:</label>  
	  <?php
		
		echo ('<script language="javascript" src="lib/calendar/calendar.js"></script>');

		$myCalendar = new tc_calendar("date5", true, false);
	  	$myCalendar->setIcon("calendar/images/iconcalendar.gif");
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath("lib/calendar/");
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	  	#$myCalendar->setSpecificDate(array("2011-04-10", "2011-04-14"), 0, 'month');
	  	#$myCalendar->setSpecificDate(array("2011-06-01"), 0, '');
	 	$myCalendar->writeScript();
		?>
		<br/>
		<br/>
      <input type="submit" name="submit" value="Save">	
    </form>
	
    </div>
      <?php echo GetPageFoot("","");?></div>