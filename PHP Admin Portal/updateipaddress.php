<?php

$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
 
function CreateDropDown($connectString){
$rowid;
if(isset($_GET['rowid'])){
	$rowid = $_GET['rowid'];
}
$query = 'select customerid from customermaster order by customerid;';
$db = pg_connect($connectString);
$result = pg_query($db, $query);
if (!$result) {
	echo pg_last_error();
	exit();
}
$selectedcustomer = '';
if(isset($rowid)){
	$queryIPowner = "SELECT customerid from ipaddressmaster where rowid = '".$rowid."';";
	$resultIPowner = pg_query($queryIPowner) or die(print pg_last_error());
	$selectedcustomer = pg_fetch_result($resultIPowner, 0, 0);
}
$dropdown = '<select name="customerid">';


while($myrow = pg_fetch_assoc($result)) {
	
	if ($myrow['customerid'] == $selectedcustomer) {
		$dropdown .= '<option value="'.$myrow['customerid'].'" SELECTED >'.$myrow['customerid'].'</option>';
	}
	else {
		$dropdown .= '<option value="'.$myrow['customerid'].'">'.$myrow['customerid'].'</option>';
	}	
}

	$dropdown .= '</select>';
	return $dropdown;
}

 if (isset($_POST['submit'])) {

     $customerid = pg_escape_string($_POST['customerid']);
     $ipaddress = pg_escape_string($_POST['ipaddress']);


	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }

                                      
     $sql = "update ipaddressmaster set customerid = '$customerid' where ipaddress = '$ipaddress';";
     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<font color='red'>Data successfully updated!</font><br/><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
 }

 else {

	$rowid = $_GET['rowid'];

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
	
     $sql = "select * from ipaddressmaster where rowid = '$rowid';";

     $result = pg_query($sql);

     $myrow = pg_fetch_assoc($result);
	
     global $rowidnew;
     $rowidnew = $myrow['rowid'];	

     pg_free_result($result);
    
     pg_close($db);

 }

	echo GetPageHead("Update IP Address", "listipaddresses.php");	
 ?>
</head>
     <div id="body"> 

    	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      	<label>IP Address:</label><input type="text" name="ipaddress" value="<?php if (isset($_POST['submit'])) { echo $ipaddress; } else { echo $myrow['ipaddress'];} ?>" READONLY STYLE="border: 0px"><br /> 
      	<label>CustomerID:</label><?php echo CreateDropDown($connectstring); ?><br /> 	        
	<input type="submit" name="submit" value="Save"></td></tr>
      	</table>
    	</form>
	<br/>
	<br/>  
     </div>

<?php echo GetPageFoot("","");?>