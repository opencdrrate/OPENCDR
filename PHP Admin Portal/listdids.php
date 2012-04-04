
<?php

	include 'config.php';
	include 'lib/Page.php';

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>DID</th>
<th>CustomerID</th>
<th></th>
<th></th>
</tr>
</thead>
<tbody>
HEREDOC;
      
	$db = pg_connect($connectstring);

	$query = 'SELECT * FROM "didmaster";';

	$csv_output = "";
	$csv_hdr = "DID|CustomerID";
	$csv_hdr .= "\n";

	$result = pg_query($query);

	while($myrow = pg_fetch_assoc($result)) {
 
$htmltable .= <<<HEREDOC
<tr>
<td>{$myrow['did']}</td>
<td>{$myrow['customerid']}</td>
<td class="actions"><a href=updatedid.php?rowid={$myrow['rowid']} class="btn-action update">Update</a></td>
<td class="actions"><a href=javascript:confirmDelete("{$myrow['rowid']}") class="btn-action delete">Delete</a></td>
</tr>\n
HEREDOC;

	$csv_output .= $myrow['did']. "|". $myrow['customerid']. "\n";
 
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
		window.location = "deletedid.php?rowid="+rowid;
		return true ;
	}
	else{
		window.location = "listdids.php";
		return false ;	
	}
}
</script>
HEREDOC;
 ?>
 
<?php echo GetPageHead("List DIDs","main.php",$javaScripts);?>
	<div id="body">
	<p>Adding all your DIDs to the inventory will allow us to match the CDR to the proper customer. Be sure to use E.164 format."</p>
	<br/>
	<br/>
	<form action="adddid.php">
	<input type="submit" class="btn blue add-customer" value="Add Manual DID"> 
	</form>

    <form name="export" action="exportpipe.php" method="post">
		<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
		<input type="hidden" name="filename" value="DIDExport.csv">
	</form>
	<?php echo $htmltable; ?>
	</div>
	<?php echo GetPageFoot();?>
