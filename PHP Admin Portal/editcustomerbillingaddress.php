<?php

	include 'config.php';
	include 'lib/Page.php';

	function check_input($data, $id, $problem='')
	{
    		$data = trim($data);
    		$data = stripslashes($data);
    		$data = htmlspecialchars($data);

    		if ($problem && strlen($data) == 0)
    		{
			echo '<p><bold>CustomerID is required.</bold></p>';
    		}
    		return $data;
	}

 if (isset($_POST['submit'])) {	

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
    
     $customerid = pg_escape_string($_POST['customerid']);
     $address1 = pg_escape_string($_POST['address1']);
     $address2 = pg_escape_string($_POST['address2']);
     $city = pg_escape_string($_POST['city']);
     $state = pg_escape_string($_POST['state']);
     $country = pg_escape_string($_POST['country']);
     $zip = pg_escape_string($_POST['zip']);

     $sqldelete = "delete from customerbillingaddressmaster where customerid = '$customerid';";

     $result = pg_query($db, $sqldelete);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
                                      
     $sql = "INSERT INTO customerbillingaddressmaster (customerid, address1, address2, city, stateorprov, country, zipcode) VALUES ('$customerid', '$address1', '$address2', '$city', '$state', '$country', '$zip')";

     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<br/><font color='red'>Data successfully added!</font><br/><br/>";
    
     pg_free_result($result);
    
     pg_close($db);

 }
 else {

	$customerid = $_GET['customerid']; 

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
	
     $sql = "select * from customerbillingaddressmaster where customerid = '$customerid';";

     $result = pg_query($sql);

     $myrow = pg_fetch_assoc($result);
	
     pg_free_result($result);
    
     pg_close($db);

 }
 ?> 
<head>
<?php echo GetPageHead("Customer Billing Address", "listcustomers.php")?>
</head>
   <div id="body">

      <form name="addressform" id="standardform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">	
      <label>CustomerID:</label><input type="text" name="customerid" value="<?php echo $customerid; ?>" READONLY STYLE="border: 0px"><br />
      <label>Address 1:</label><input type="text" name="address1" value="<?php if (isset($_POST['submit'])) { echo $address1; } else { echo $myrow['address1']; } ?>" ><br />
      <label>Address 2:</label><input type="text" name="address2" value="<?php if (isset($_POST['submit'])) { echo $address2; } else { echo $myrow['address2']; } ?>" ><br />
      <label>City:</label><input type="text" name="city" value="<?php if (isset($_POST['submit'])) { echo $city; } else { echo $myrow['city']; } ?>" ><br />
      <label>State/Province:</label><input type="text" name="state" value="<?php if (isset($_POST['submit'])) { echo $state; } else { echo $myrow['stateorprov']; } ?>" ><br />
      <label>Country:</label><input type="text" name="country" value="<?php if (isset($_POST['submit'])) { echo $country; } else { echo $myrow['country']; } ?>" ><br />
      <label>Zip:</label><input type="text" name="zip" value="<?php if (isset($_POST['submit'])) { echo $zip; } else { echo $myrow['zipcode']; } ?>" ><br />
      <input type="submit" name="submit" value="Save">	
      </form> 

   </div>
      <?php echo GetPageFoot("","");?></div>