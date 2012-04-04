 <?php

	include 'config.php';
	include 'lib/Page.php';

function CreateDropDown($connectString){
$query = 'select customerid from customermaster order by customerid;';
$db = pg_connect($connectString);
$result = pg_query($db, $query);
if (!$result) {
	echo pg_last_error();
	exit();
}

$dropdown = '<select name="customerid">';
$dropdown .= '<option></option>';

while($myrow = pg_fetch_assoc($result)) { 
	$dropdown .= '<option value="'.$myrow['customerid'].'">'.$myrow['customerid'].'</option>';
}

$dropdown .= '</select>';

return $dropdown;
}

 if (isset($_POST['submit'])) { 

	$db = pg_connect($connectstring);
        if (!$db) {
         die("Error in connection: " . pg_last_error());
     }
    
     $customerid = pg_escape_string($_POST['customerid']);
     $calltype = pg_escape_string($_POST['calltype']);
     $taxtype = pg_escape_string($_POST['taxtype']);
     $taxrate = pg_escape_string($_POST['taxrate']);

                                      
     $sql = "INSERT INTO \"customertaxsetup\" (customerid, calltype, taxtype, taxrate) VALUES ('$customerid', $calltype, '$taxtype', $taxrate)";
     $result = pg_query($db, $sql);
     if (!$result) {
         die("Error in SQL query: " . pg_last_error());
     }
    
     echo "<br/><font color='red'>Data successfully added!</font><br/>";
    
     pg_free_result($result);
    
     pg_close($db);
 }
 ?> 
 <?php echo GetPageHead("Add Customer Tax Setup", "listcustomertaxsetup.php")?>

   <div id="body">  
	<table>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="standardform">
      <tr><td><label>CustomerID:</label></td><td><?php echo CreateDropDown($connectstring); ?></td></tr> 
      <tr><td><label>Call Type:</label></td><td>
	  <select name="calltype">
      <option value="5" <?php if (isset($_POST['submit'])) { if ($calltype == "5") { echo "selected"; }} ?>>Intrastate</option>
      <option value="10" <?php if (isset($_POST['submit'])) { if ($calltype == "10") { echo "selected"; }} ?>>Interstate</option>
      </select></td></tr>     
      <tr><td><label>Tax Type:</label></td><td><input type="text" name="taxtype" value="<?php if (isset($_POST['submit'])) { echo $taxtype; } ?>" ></td></tr>       
      <tr><td><label>Tax Rate:</label></td><td><input type="text" name="taxrate" value="<?php if (isset($_POST['submit'])) { echo $taxrate; } ?>" ></td></tr>        
      <tr><td><input type="submit" name="submit" value="Save"></td><td></td></tr>	
    </form>
	</table>	
    </div>
   
   <?php echo GetPageFoot("","");?>