<?php

	include 'config.php';
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';

 if (isset($_POST['submit'])) {

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
    
     $customerid = pg_escape_string($_POST['customerid']);
     $did = pg_escape_string($_POST['did']);
                      
     $sql = "INSERT INTO didmaster (did, customerid) VALUES ('$did', '$customerid')";
     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<font color='red'>Data successfully added!</font><br/><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
 }
 ?> 
<?php echo GetPageHead("Add DID", "listdids.php")?>
</head>
   <div id="body">  

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <label>DID:</label><input type="text" name="did" value="<?php if (isset($_POST['submit'])) { echo $did; } ?>" ><br /> 
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring,'customerid', 'customermaster'); ?><br />  
      <input type="submit" name="submit" value="Save"><br />	
    </form> 
    </div>
   
   <?php echo GetPageFoot("","");?>