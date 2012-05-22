<?php
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>CustomerID</th>
<th>Call Type</th>
<th>TaxType</th>
<th>TaxRate</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HEREDOC;
	
	$db = pg_connect($connectstring);

	$query = 'select customerid, vwcalltypes."CallTypeDesc", taxtype, taxrate, rowid
                  from customertaxsetup left outer join vwcalltypes on (customertaxsetup.calltype = vwcalltypes."CallType")
                  order by customerid;';

	$csv_output = "";
	$csv_hdr = "CustomerID|CallType|TaxType|TaxRate";
	$csv_hdr .= "\n";
	 
	$result = pg_query($query); 
        if (!$result) { 
            echo "Problem with query " . $query . "<br/>"; 
            echo pg_last_error(); 
            exit(); 
        } 

	while($myrow = pg_fetch_assoc($result)) { 

$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['customerid']}</td>
<td>{$myrow['CallTypeDesc']}</td>
<td>{$myrow['taxtype']}</td>
<td>{$myrow['taxrate']}</td>
<td class="actions">
<a href=updatecustomertaxsetup.php?rowid={$myrow['rowid']} class="btn-action update">Update</a></td>
<td class="actions">
<a href=javascript:confirmDelete("{$myrow['rowid']}") class="btn-action delete">Delete</a></td>
</td></tr>\n
HEREDOC;
	
	$csv_output .= $myrow['customerid']. "|". $myrow['CallTypeDesc']. "|". $myrow['taxtype']. "|". $myrow['taxrate']. "\n";	
	
	}

	$htmltable .= '
	    </tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="6"></td>
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
		window.location = "deletecustomertaxsetup.php?rowid="+rowid;
		return true ;
	}
	else{
		window.location = "listcustomertaxsetup.php";
		return false ;	
	}
}
</script>
HEREDOC;
 ?>

	<?php echo GetPageHead("List Customer Tax Setup","main.php",$javaScripts);?>
	<div id="body">
	
	<form action="addcustomertaxsetup.php">
		<input type="submit" class="btn blue add-customer" value="Add Tax Setup"> 
	</form>
	
    <form name="export" action="exportpipe.php" method="post">
		<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
		<input type="hidden" name="filename" value="CustomerTaxSetupExport.csv">
	</form>
	<?php echo $htmltable; ?>
	
	</div>
	<?php echo GetPageFoot();?>