<?php

$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'DAL/table_didmaster.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
	$result = '';
	$table = new psql_didmaster($connectstring);
	$table->Connect();
 if (isset($_POST['submit'])) {

     $customerid = pg_escape_string($_POST['customerid']);
     $did = pg_escape_string($_POST['did']);
	 
	 $table->Update(array('did'=>$did), array('customerid'=>$customerid));
	 
	 $result = 'Row updated!';
 }

 else {
	$rowid = $_GET['rowid'];

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
	
     $sql = "select * from didmaster where rowid = '$rowid';";

     $result = pg_query($sql);

     $myrow = pg_fetch_assoc($result);
	
     global $rowidnew;
     $rowidnew = $myrow['rowid'];	

     pg_free_result($result);
    
     pg_close($db);

 }	
 $table->Disconnect();
 ?>
<?php echo GetPageHead("Update DID", "listdids.php")?>
</head>
   <div id="body"> 
	<?php echo $result;?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <label>DID:</label><input type="text" name="did" value="<?php if (isset($_POST['submit'])) { echo $did; } else { echo $myrow['did'];} ?>" READONLY STYLE="border: 0px"><br />
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring,'customerid', 'customermaster'); ?><br /> 	        
      <input type="submit" name="submit" value="Save"><br />
    </form> 
   </div>

   <?php echo GetPageFoot("","");?>