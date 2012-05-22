<?php
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';

	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/calendar/classes/tc_calendar.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

function GenerateCustomerIDDropDown($connectstring){
	$dropDown = '';
	$dropDown .= "Customer ID :"; 
	$dropDown .= '<select name="customerid">';
	$dropDown .= '<option value="">Choose a Customer</option>';
		$db = pg_connect($connectstring);

		$customerIDQuery = "select CustomerID from customermaster order by customerid;";
		$customerQueryResults = pg_query($customerIDQuery);
		$customerids = pg_fetch_all($customerQueryResults);
		
		foreach($customerids as $id){
			$customerid = $id['customerid'];
			$dropDown .= "<option value=\"'.$customerid.'\">'".$customerid."'</option>";
		}
		pg_close($db);
		
	$dropDown .= "</select>";
	$dropDown .= "<br>";
	$dropDown .= "<br>";
	return $dropDown;
}

$date = date('y-m-d');
$amount = '0';
$type = '';
$note = '';
$error = '';
if(isset($_POST['function'])){
	$function = $_POST['function'];
	$content = '';
	if($function == 'sendPayment'){
		$customerid = $_GET['customerid'];
		$date = $_REQUEST['date'];
		$amount = $_POST['paymentAmount'];
		$type = $_POST['paymentType'];
		$note = $_POST['paymentNote'];
		
		#validate input
		if(!is_numeric($amount)){
			$error .= 'Amount must be a number<br>';
			$amount = 0;
		}
		else if($amount < 0){
			$error .= 'Amount must be greater than zero<br>';
			$amount = 0;
		}
		if(strlen($customerid) == 0){
			$error .= 'Please choose a customer<br>';
		}
		if(strlen($type) >20){
			$error .= 'The maximum character length for the payment type is 20<br>';
		}
		if(strlen($note)>100){
			$error .= 'The maximum character length for the payment note is 100<br>';
		}
	}

if(strlen($error) ==0){
$rowVals = array("'".$customerid."'", "'".$date."'", "'".$amount."'","'".$type."'","'".$note."'");
$db = pg_connect($connectstring);
$insertQuery = "INSERT INTO paymentmaster(
            customerid, paymentdate, paymentamount, paymenttype, paymentnote)
    VALUES (".implode($rowVals,",").");";
pg_query($insertQuery);
header('location: listpayments.php?customerid='.$customerid);
echo '<!--';
}
}
$customerid = $_GET['customerid'];
?>

<html>
<head>
<script language="javascript" src="lib/calendar/calendar.js"></script>
<?php echo GetPageHead("Add a Payments", "listcustomers.php")?>
</head>
   <div id="body"> 

<font color="#FF0000"><?php echo $error;?></font>
<form action="addpayment.php?customerid=<?php echo $customerid;?>" method="post" id="standardform">
	<input type="hidden" name="function" value="sendPayment"/>
	<div style="float: left;"><label>Payment Date:</label>  	</div>
<?php
//get class into the page

	  $myCalendar = new tc_calendar("date",true,true);
	  if(strlen($date) == 0){
		$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  }
	  else{
		$time = strtotime($date);
		$myCalendar->setDate(date('d',$time), date('m',$time), date('Y',$time));
	  }
	  
	  $myCalendar->setIcon("lib/calendar/images/iconCalendar.gif");
	  $myCalendar->setPath("lib/calendar/");
	  $myCalendar->writeScript();
?>
<br><br>
	<label>Payment Amount:</label> <input type="text" name="paymentAmount" value="<?php echo $amount;?>"/>
<br><br>
	<label>Payment Type:</label> <input type="text" name="paymentType" value="<?php echo $type;?>" maxlength=20 size=25/>
<br><br>
	<label>Payment Note:</label> <input type="text" name="paymentNote" value="<?php echo $note;?>" maxlength=100 size=105/>
	<br><br>
	<input type="submit" value="Save"/>
</form>

</div>
      <?php echo GetPageFoot("","");?></div>
</html>