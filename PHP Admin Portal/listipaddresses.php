
<?php
include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
	$db = pg_connect($connectstring);
if(isset($_GET['delete'])){
	$rowid = $_GET['delete'];
	
	$deleteStatement = "DELETE FROM ipaddressmaster WHERE rowid=".$rowid;
	pg_query($deleteStatement);
}
$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>IP Address</th>
<th>CustomerID</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HEREDOC;


   	$query = 'SELECT * FROM "ipaddressmaster";';
	$partialQuery = 'SELECT ipaddress, customerid FROM ipaddressmaster;';
	$titles = preg_split("/,/","IPAddress,CustomerID",-1);
	$queryResult = SQLSelectQuery($connectstring, $partialQuery, ",", "\n");
	#$htmltable = QueryResultToTable($queryResult, ",",$titles);

	$result = pg_query($query); 
        if (!$result) { 
            echo "Problem with query " . $query . "<br/>"; 
            echo pg_last_error(); 
            exit(); 
        }

	while($myrow = pg_fetch_assoc($result)) {
		
$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['ipaddress']}</td>
<td>{$myrow['customerid']}</td>
<td class="actions">
<a href=updateipaddress.php?rowid={$myrow['rowid']} class="btn-action update">Update</a></td>
<td class="actions">
<a href=javascript:confirmDelete({$myrow['rowid']}) class="btn-action delete">Delete</a></td>
</td></tr>\n
HEREDOC;

}
	$htmltable .= '
	   </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="4"></td>
	    	</tr>
	    </tfoot>
	   </table>';


        ?>	 

<?php

$javaScripts = <<< HEREDOC
<script type="text/javascript">
function confirmDelete(rowid){
	var agree=confirm("Are you sure you want to delete this row?");
	if (agree){
		window.location = "listipaddresses.php?delete="+rowid;
		return true ;
	}
	else{
		window.location = "listipaddresses.php";
		return false ;	
	}
}
</script>
HEREDOC;
 ?>
	<?php echo GetPageHead("List IP Addresses", "main.php", $javaScripts);?>
	<body>
	<div id="body">
	<br/>
	<p>Adding all the IP addresses you accept traffic from will allow us to match the CDR to the proper customer.</p>
	<br/>

	<form name="addipaddress" action="addipaddress.php" method="post">
		<input type="hidden" name="function" value="add"/>		
		<input type="submit" class="btn blue add-customer" value="Add IP Address"/>
	</form>

    	<form name="export" action="exportpipe.php" method="post">
		<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
		<input type="hidden" name="filename" value="IPAddressExport.csv">
	</form>
	
	<?php echo $htmltable; ?>
	<br/>
	<br/>
	</div>
	</body>
	<?php echo GetPageFoot();?>