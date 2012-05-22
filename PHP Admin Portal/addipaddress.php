 <?php 
	include_once 'config.php';
	$defaultIPValue = '';
	$errors = '';
	$content = '';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
function customError($errno, $errstr){
	global $errors;
	$errors .= <<<HEREDOC
	<font color='red'>{$errstr}</font><br>
HEREDOC;
}
function CreateDropDown($connectString){
$query = 'select customerid from customermaster order by customerid;';
$db = pg_connect($connectString);
$result = pg_query($db, $query);
if (!$result) {
	trigger_error( pg_last_error());
	exit();
}
$dropdown = '<select name="customerid">';
$count = 0;
while($myrow = pg_fetch_assoc($result)) { 
	$count++;
	$dropdown .= '<option value="'.$myrow['customerid'].'">'.$myrow['customerid'].'</option>';
}

$dropdown .= '</select>';

if($count == 0){
	trigger_error("<font color='red'>No customers!</font><br>");
}
return $dropdown;
}
	set_error_handler("customError");
	
	$dropDownMenu = CreateDropDown($connectstring);
 	if (isset($_POST['submit'])) {

     $customerid = pg_escape_string($_POST['customerid']);
     $ipaddress = pg_escape_string($_POST['ipaddress']);
     
     if($ipaddress == $defaultIPValue or $ipaddress == ''){
		trigger_error( "Invalid IPAddress entered!" );
	 }
	 else if($customerid == ''){
		trigger_error( "Please choose a customer." );
	 }
	 else{
		$db = pg_connect($connectstring);
			if (!$db) {
				trigger_error("Error in connection: " . pg_last_error());
		}
     $sql = "INSERT INTO ipaddressmaster (ipaddress, customerid) VALUES ('$ipaddress', '$customerid')";
     $result = pg_query($db, $sql);
     if (!$result) {
         trigger_error("Error in SQL query: " . pg_last_error());
     }
	 else{
		$content .= "<font color='red'>Data successfully added!</font><br/><br/>";
     }
     pg_free_result($result);
    
     pg_close($db);
	 }
 }

	if (isset($_GET['ipaddress'])){
		$defaultIPValue = $_GET['ipaddress'];
	}
 ?> 
	<?php echo GetPageHead("Add IP Address", "listipaddresses.php");?>
	<div id="body">
    <br />
    <link rel="stylesheet" type="text/css" media="screen" href="stylesheets/style.css" />

    <style type="text/css">

    #searchform label {float: left;padding: 7px 0 0 0;width: 80px;
 }

    </style>	
	<?php echo $errors;?>
	<?php echo $content;?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">  
      <label>IP Address:</label><input type="text" name="ipaddress" value="<?php if (isset($_POST['submit'])) { echo $ipaddress; } else { echo $defaultIPValue;} ?>" ><br />
      <label>CustomerID:</label><?php echo CreateDropDown($connectstring); ?><br /> 
      <input type="submit" name="submit" value="Save">	
    </form> 

</div>
   <?php echo GetPageFoot("","");?>