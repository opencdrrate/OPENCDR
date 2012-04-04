
<?php
include 'lib/Page.php';
$interstateconfpage="vars/RateConfPages/interstateconfig.php";
$intrastateconfpage="vars/RateConfPages/intrastateconfig.php";
$internationalconfpage="vars/RateConfPages/internationalconfig.php";
$tieredorigconfpage="vars/RateConfPages/tierorigconfig.php";
$tollfreeconfpage="vars/RateConfPages/tollfreeconfig.php";
$simpletermconfpage="vars/RateConfPages/simpletermconfig.php";

$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>Customer ID</th>
<th>Recurring Charges</th>
<th>One-Time Charges</th> 
<th>Interstate</th> 
<th>Intrastate</th>
<th>Tiered Origination</th>
<th>International</th>
<th>Toll-free Origination</th>
<th>Simple Termination</th>
</tr>
</thead>
<tbody>
HEREDOC;

        include 'config.php';

	$db = pg_connect($connectstring);

        $query = 'select customerid from customermaster order by customerid;';

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
<td align="center"><a href=listcustomerrecurringcharges.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=listcustomeronetimecharges.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$interstateconfpage}&load=1>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$intrastateconfpage}&load=1>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$tieredorigconfpage}&load=1>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$internationalconfpage}&load=1>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$tollfreeconfpage}&load=1>Edit</a></td>
<td align="center"><a href=ratequery.php?customerid={$myrow['customerid']}&confpage={$simpletermconfpage}&load=1>Edit</a></td>
</tr>
HEREDOC;
        
	}

	$htmltable .= <<< HEREDOC
	</tbody>
	<tfoot>
	<tr>
	<td colspan="9"></td>
	</tr>
	</tfoot>
	</table>
HEREDOC;
 
        ?>
	
	<?php echo GetPageHead("Edit Customer Rates");?>
	
    <div id="body">
	<?php echo $htmltable; ?>
	</div>
	<?php echo GetPageFoot();?>