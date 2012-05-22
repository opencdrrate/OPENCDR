
<?php 
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/Page.php';		
	include_once $path . 'lib/psql_connection.php';
	
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
	$testConnection = new psql_connection();
	$isValid = $testConnection->TestConnectstring($connectstring);
	if(!$isValid){
		header('location: configurationpage.php');
		exit();
	}
	
	$sitename = $manager->GetSetting('sitename');
	$db = pg_connect($connectstring);

    $queryHELDCalls = 'SELECT count(*) FROM callrecordmaster_held;';
	$queryCalls = 'SELECT count(*) FROM callrecordmaster;';
	$queryTBRCalls = 'SELECT count(*) FROM callrecordmaster_tbr where calltype is not null;';
    $queryCustomers = 'SELECT count(*) FROM customermaster;'; 
	$queryProcesses = 'SELECT count(*) FROM processhistory;'; 
	$queryRateCenters = 'SELECT count(*) FROM tieredoriginationratecentermaster';
    $queryBillBatches = 'SELECT count(*) FROM billingbatchmaster;';
    $queryIPAddress = 'SELECT count(*) FROM ipaddressmaster;'; 
	$queryCustomerTaxSetup = 'SELECT count(*) FROM customertaxsetup;';
	$queryDIDs = 'SELECT count(*) FROM didmaster;';
	$queryCDRuncat = 'select count(*) from callrecordmaster_tbr where calltype is null;';
	$queryNPAmaster = 'select count(*) from npamaster;';
    $resultHELDCalls = pg_query($queryHELDCalls) or die(print pg_last_error());
	$resultCalls = pg_query($queryCalls) or die(print pg_last_error());
	$resultTBRCalls = pg_query($queryTBRCalls) or die(print pg_last_error());
	$resultCustomers = pg_query($queryCustomers) or die(print pg_last_error());
	$resultProcesses = pg_query($queryProcesses) or die(print pg_last_error());
	$resultRateCenters = pg_query($queryRateCenters) or die(print pg_last_error());
	$resultBillBatches = pg_query($queryBillBatches) or die(print pg_last_error());
	$resultIPAddress = pg_query($queryIPAddress) or die(print pg_last_error()); 
	$resultNPAs = pg_query($queryNPAmaster) or die(print pg_last_error());
	$resultCTS = pg_query($queryCustomerTaxSetup) or die(print pg_last_error());
	$resultDIDs = pg_query($queryDIDs) or die(print pg_last_error());
	$resultCDRuncat = pg_query($queryCDRuncat) or die(print pg_last_error());

	pg_close($db);
?>

    <?php echo GetPageHead($sitename);?>

	<div id="body">
        <table id="listing-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    Rating Errors:
                </td>
		<td>
			<a href="listresultsheld.php"><?php print number_format(pg_fetch_result($resultHELDCalls, 0, 0), 0, '', ','); ?></a>

	    </td>
            </tr>
	    <tr>
                <td>
                    Uncategorized CDR:
                </td>
		<td>

			<a href="listresultsuncatcdr.php"><?php print number_format(pg_fetch_result($resultCDRuncat, 0, 0), 0, '', ','); ?></a>

	    </td>
            </tr>
	    <tr>
                <td>
                    Rating Queue:
                </td>
		<td>

			<a href="listresultstbr.php"><?php print number_format(pg_fetch_result($resultTBRCalls, 0, 0), 0, '', ','); ?></a>

	    </td>
            </tr>
	    <tr>
                <td>
                    Rated Calls:
                </td>
		<td>

			<a href="searchcrm.php"><?php print number_format(pg_fetch_result($resultCalls, 0, 0), 0, '', ','); ?></a>

	    </td>
            </tr>

            <tr>
                <td>
                    Customers:
                </td>
		<td>
 
			<a href="listcustomers.php"><?php print number_format(pg_fetch_result($resultCustomers, 0, 0), 0, '', ','); ?></a>

	    </td>
            </tr>

		<tr>
                <td>
                    Customer Rates:
                </td>
		<td>
 
			<a href="rates.php" class="edit">Edit</a>

	    </td>
            </tr>
            <tr>
            <tr>
	
                <td>
                    Customer Tax Setup:
                </td>
                <td>
                    <a href="listcustomertaxsetup.php"><?php print number_format(pg_fetch_result($resultCTS, 0, 0), 0, '', ','); ?></a>
                </td>
            </tr>
	    <tr>
                <td>
                    DIDs:
                </td>
                <td>

			<a href="listdids.php"><?php print number_format(pg_fetch_result($resultDIDs, 0, 0), 0, '', ','); ?></a>

                </td>
            </tr>
            <tr>
                <td>
                    IP Addresses:
                </td>
                <td>

			<a href="listipaddresses.php"><?php print number_format(pg_fetch_result($resultIPAddress, 0, 0), 0, '', ','); ?></a>

                </td>
            </tr>
			<tr>
				<td>
					NPA Information:
				</td>
				<td>
				<a href="npamaster.php"><?php print number_format(pg_fetch_result($resultNPAs, 0, 0), 0, '', ','); ?></a>
				</td>
			</tr>
            <tr>
                <td>
                    Billing Batches:
                </td>
                <td>

                	<a href="listbillbatches.php"><?php print number_format(pg_fetch_result($resultBillBatches, 0, 0), 0, '', ','); ?></a>

                </td>
            </tr>
            <tr>
                <td>
                    Process History:
                </td>
                <td>
 
			<a href="listprocesshistory.php"><?php print number_format(pg_fetch_result($resultProcesses, 0, 0), 0, '', ','); ?></a>

                </td>
            </tr>
            <tr>
                <td>
                    Rate Centers:
                </td>
                <td>
 
			<a href="listratecenters.php"><?php print number_format(pg_fetch_result($resultRateCenters, 0, 0), 0, '', ','); ?></a>

                </td>
            </tr>
	    <tr>
                <td>
                    Reports:
                </td>
                <td>
                   <a href="reports.php">17</a>
                </td>
            </tr>
	    <tr>
                <td>
                </td>
                <td>
                   <a href="adhocquery.php" target="_blank">ADHOC Query</a>
                </td>
            </tr>
            <tr>
                <td>
                    Please visit us at:
                </td>
                <td>
                   <a href="http://www.opencdrrate.org/" target="_blank">http://www.opencdrrate.org/</a>
                </td>
            </tr>
			<tr>
				<td></td>
				<td>
					<a href="configurationpage.php">Configuration</a>
				</td>
			</tr>
        </table>
	<br/>
	<br/>
	<br/>
    </div>
    <?php echo GetPageFoot("","");?>