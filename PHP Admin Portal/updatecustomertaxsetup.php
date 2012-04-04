<?php

	include 'lib/Page.php';
	include 'config.php';

	global $calltypedesc;

 if (isset($_POST['submit'])) {

	$customerid = pg_escape_string($_POST['customerid']);
	$calltype = pg_escape_string($_POST['calltype']);
	$taxtype = pg_escape_string($_POST['taxtype']);
	$newtaxrate = pg_escape_string($_POST['taxrate']);
	$calltypedesc = pg_escape_string($_POST['calltypedesc']);

	include 'config.php'; 

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }

                                      
     $sql = "update customertaxsetup set taxrate = '$newtaxrate' where customerid = '$customerid' and calltype = '$calltype' and taxtype = '$taxtype';";
     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<font color='red'>Data successfully updated!</font><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
 }

 else {
	$rowid = $_GET['rowid'];

	include 'config.php'; 

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
	
     #$sql = "select * from customertaxsetup where rowid = '$rowid';";
     $sql = "select customerid, calltype, \"CallTypeDesc\", taxtype, taxrate, rowid
             from customertaxsetup left outer join vwcalltypes on (customertaxsetup.calltype = vwcalltypes.\"CallType\")
             where rowid = '$rowid';";	

     $result = pg_query($sql);

     $myrow = pg_fetch_assoc($result);	

     pg_free_result($result);
    
     pg_close($db);

 }
	
 ?>

	<?php echo GetPageHead("Update Customer Tax Setup", "listcustomertaxsetup.php");?>
	<div id="body"> 

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <label>CustomerID:</label><input type="text" name="customerid" value="<?php if (isset($_POST['submit'])) { echo $customerid; } else {echo $myrow['customerid'];} ?>" READONLY STYLE="border: 0px"></label><br/> 
      <label>Call Type:</label><input type="text" name="calltypedesc" value="<?php if (isset($_POST['submit'])) { echo $calltypedesc; } else {echo $myrow['CallTypeDesc'];} ?>" READONLY STYLE="border: 0px" size="100"></td><td><input type="hidden" name="calltype" value="<?php echo $myrow['calltype']; ?>"><br/>       
      <label>Tax Type:</label><input type="text" name="taxtype" value="<?php if (isset($_POST['submit'])) { echo $taxtype; } else {echo $myrow['taxtype'];} ?>" READONLY STYLE="border: 0px" ><br/>        
      <label>Tax Rate:</label><input type="text" name="taxrate" value="<?php if (isset($_POST['submit'])) { echo $newtaxrate; } else {echo $myrow['taxrate'];} ?>" ><br/>        
      <input type="submit" name="submit" value="Save">
    </form> 
   
   </div>
	<?php echo GetPageFoot();?>