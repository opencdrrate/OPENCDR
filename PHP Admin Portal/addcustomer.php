<?php

	include 'config.php';
	include 'lib/Page.php';		

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
     	$customername = pg_escape_string($_POST['customername']);
     	$lrn = pg_escape_string($_POST['lrn']);
     	$cnam = pg_escape_string($_POST['cnam']);
     	$calltype = pg_escape_string($_POST['calltype']);
     	$bcycle = pg_escape_string($_POST['bcycle']);
     	$email = pg_escape_string($_POST['email']);

	if ($customerid == "") {
	
		$good = BadEntry("CustomerID is required");
     	}

     	else if ($customername == "") {

		$good = BadEntry("Customer Name is required");
     	}

	else if ($email <> "") {

		if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
    			$good = BadEntry("Email is not in a valid format");
		}
	}

	else if (!is_numeric($lrn)) {

		$good = BadEntry("LRN must be numeric");
	}

	else if (!is_numeric($cnam)) {

		$good = BadEntry("CNAM must be numeric");
	}

	else if ($bcycle == "") {
	
		$good = BadEntry("Billing Cycle is required");
     	}

	if ($good == 0) {

                                      
     		$sql = "INSERT INTO customermaster (customerid, customername, lrndiprate, cnamdiprate, indeterminatejurisdictioncalltype, billingcycle) VALUES ('$customerid', '$customername', '$lrn', '$cnam', $calltype, '$bcycle')";
     		$result = pg_query($db, $sql);
	 
    		$insertEmailStatement = "INSERT INTO customercontactmaster (customerid,primaryemailaddress) VALUES ('$customerid','$email')";
     		$result2 = pg_query($db, $insertEmailStatement);
	 
		if (!$result or !$result2) {
         		die("Error in SQL query: " . pg_last_error());
     		}
    
        	pg_free_result($result);
    
     		pg_close($db);

		echo ("<script type='text/javascript'>");
		echo ('window.location = "listcustomers.php";');
		echo ("</script>");

	}

 }

 ?> 
<?php echo GetPageHead("Add Customer", "listcustomers.php")?>
</head>
   <div id="body">  
	<table>
    <form name="customerform" id="standardform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <tr><td><label>CustomerID:</label></td>		<td><input type="text" name="customerid" value="<?php if (isset($_POST['submit'])) {echo $customerid;} ?>" ></td></tr>
      <tr><td><label>Customer Name:</label></td>	<td><input type="text" name="customername" value="<?php if (isset($_POST['submit'])) {echo $customername;} ?>" ></td></tr>
      <tr><td><label>E-mail:</label></td>		<td><input type="text" name="email" value="<?php if (isset($_POST['submit'])) { echo $email; } ?>" ></td></tr>
      <tr><td><label>LRN Dip Rate:</label></td>		<td><input type="text" name="lrn" value="<?php if (isset($_POST['submit'])) { echo $lrn; } else { echo 0; } ?>" ></td></tr>  
      <tr><td><label>CNAM Dip Rate:</label></td>	<td><input type="text" name="cnam" value="<?php if (isset($_POST['submit'])) { echo $cnam; } else { echo 0; } ?>" ></td></tr>
      <tr><td><label>Billing Cycle:</label></td>	<td><input type="text" name="bcycle" value="<?php if (isset($_POST['submit'])) { echo $bcycle; } ?>" ></td></tr>
      <tr><td><label>Indeterminate Call Type:</label></td><td>
      <select name="calltype">
      <option value="5" <?php if (isset($_POST['submit'])) { if ($calltype == "5") { echo "selected"; }} ?>>Intrastate</option>
      <option value="10" <?php if (isset($_POST['submit'])) { if ($calltype == "10") { echo "selected"; }} ?>>Interstate</option>
      </select></td></tr>
      <tr><td><input type="submit" name="submit" value="Save"></td></tr>	
    </form>
	
	</table>
    </div>
      <?php echo GetPageFoot("","");?></div>