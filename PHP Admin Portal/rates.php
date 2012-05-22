
<?php
include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

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
<td align="center"><a href=interstaterates.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=intrastaterates.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=tieredorigrates.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=internationalrates.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=tollfreeorig.php?customerid={$myrow['customerid']}>Edit</a></td>
<td align="center"><a href=simpletermination.php?customerid={$myrow['customerid']}>Edit</a></td>
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