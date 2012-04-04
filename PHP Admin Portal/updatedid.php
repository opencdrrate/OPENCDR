<?php

	include 'config.php';
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';

 if (isset($_POST['submit'])) {

     $customerid = pg_escape_string($_POST['customerid']);
     $did = pg_escape_string($_POST['did']);


	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }

                                      
     $sql = "update \"didmaster\" set customerid = '$customerid' where did = '$did';";
     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<br/><font color='red'>Data successfully updated!</font><br/><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
 }

 else {
	$rowid = $_GET['rowid'];

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
	
     $sql = "select * from DIDMaster where rowid = '$rowid';";

     $result = pg_query($sql);

     $myrow = pg_fetch_assoc($result);
	
     global $rowidnew;
     $rowidnew = $myrow['rowid'];	

     pg_free_result($result);
    
     pg_close($db);

 }	
 ?>
<?php echo GetPageHead("Update DID", "listdids.php")?>
</head>
   <div id="body"> 

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <label>DID:</label><input type="text" name="did" value="<?php if (isset($_POST['submit'])) { echo $did; } else { echo $myrow['did'];} ?>" READONLY STYLE="border: 0px"><br />
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring,'customerid', 'customermaster'); ?><br /> 	        
      <input type="submit" name="submit" value="Save"><br />
    </form> 
   </div>

   <?php echo GetPageFoot("","");?>