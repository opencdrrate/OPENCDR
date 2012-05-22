<?php
	include_once 'config.php';

	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'DAL/table_didmaster.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
	$errors = '';
	function customError($errno, $errstr)
	{
		global $errors;
		$errors .= '<font color="red">'.$errstr.'</font><br>';
	}
	set_error_handler("customError");
	
	$result = '';
	$did = '';
	if(isset($_GET['did'])){
		$did = $_GET['did'];
	}
 if (isset($_POST['submit'])) {
	 $table = new psql_didmaster($connectstring);
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
      <label>DID:</label><input type="text" name="did" value="<?php if (isset($did)) { echo $did; } ?>" ><br /> 
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring,'customerid', 'customermaster'); ?><br />  
      <input type="submit" name="submit" value="Save"><br />	
    </form> 
    </div>
   
   <?php echo GetPageFoot("","");?>