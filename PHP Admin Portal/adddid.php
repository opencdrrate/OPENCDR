<?php

	include 'config.php';
	include 'lib/Page.php';
	include 'lib/SQLQueryFuncs.php';
	include 'DAL/table_didmaster.php';
	
	$errors = '';
	function customError($errno, $errstr)
	{
		global $errors;
		$errors .= '<font color="red">'.$errstr.'</font><br>';
	}
	set_error_handler("customError");
	
	$result = '';
	$table = new psql_didmaster($connectstring);
 if (isset($_POST['submit'])) {
     $customerid = pg_escape_string($_POST['customerid']);
     $did = pg_escape_string($_POST['did']);
	 
	 $table->Connect();
	 try{
		$insertResult = $table->Insert(array('did'=>$did, 'customerid'=>$customerid));
		
		 if($insertResult){
			$result .= "<font color='red'>Data successfully added!</font><br/><br/>";
		 }
	 }
	 
	 catch(Exception $e){
		trigger_error($e->getMessage());
	 }
	 $table->Disconnect();
 }
 ?> 
<?php echo GetPageHead("Add DID", "listdids.php")?>
   <div id="body">  
	<?php echo $errors;?>
	<?php echo $result;?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <label>DID:</label><input type="text" name="did" value="<?php if (isset($_POST['submit'])) { echo $did; } ?>" ><br /> 
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring,'customerid', 'customermaster'); ?><br />  
      <input type="submit" name="submit" value="Save"><br />	
    </form> 
    </div>
   
   <?php echo GetPageFoot("","");?>