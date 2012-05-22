 <html>
<head>

<script type="text/javascript">

function MoveHELDCDR()
{	// code for IE7+, Firefox, Chrome, Opera, Safari

  	xmlhttp=new XMLHttpRequest();

	xmlhttp.open("GET","moveheldcdrtotbr.php",true);
	xmlhttp.send();
}

</script>
</head>

    <body> 
	<a href="main.php"><--Back</a>
	<br/>
	<br/>
	<input type ="button" onclick="MoveHELDCDR()" value="Move these calls back to the rating queue" />
	<br/>
	<br/>
        <table border="4" cellspacing="5" cellpadding="0"> 
            <tr> 
                <td> 
                    Call ID 
                </td> 
                <td> 
                    Customer ID 
                </td>
		<td> 
                    Call Type 
                </td> 
		<td> 
                    Call Date Time 
                </td>
		<td> 
                    Duration 
                </td>
		<td> 
                    Direction 
                </td>
		<td> 
                    Source IP 
                </td>
                <td> 
                    Origination Number
                </td> 
                <td> 
                    Destination Number 
                </td> 
		<td> 
                    LRN 
                </td>
		<td> 
                    CNAM Dipped
                </td>
		<td> 
                    Rate Center 
                </td>
		<td> 
                    Carrier ID 
                </td>
		<td> 
                    Error Message 
                </td>
            </tr> 

        <?php 
 
	include_once 'config.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

	$db = pg_connect($connectstring);

	$start = $_POST['start'];
	$end = $_POST['end'];

        #$query = 'SELECT * FROM "CallRecordMaster_HELD";';
	$result = pg_query_params($db,'SELECT * FROM "CallRecordMaster_HELD" where cast(calldatetime as date) between $1 and $2', array($start, $end));

        $result = pg_query($query); 
        if (!$result) { 
            echo "Problem with query " . $query . "<br/>"; 
            echo pg_last_error(); 
            exit(); 
        } 

        while($myrow = pg_fetch_assoc($result)) { 
            printf ("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", $myrow['callid'], htmlspecialchars($myrow['customerid']), htmlspecialchars($myrow['calltype']), htmlspecialchars($myrow['calldatetime']), htmlspecialchars($myrow['duration']), htmlspecialchars($myrow['direction']), htmlspecialchars($myrow['sourceip']), htmlspecialchars($myrow['originatingnumber']), htmlspecialchars($myrow['destinationnumber']), htmlspecialchars($myrow['lrn']), htmlspecialchars($myrow['cnamdipped']), htmlspecialchars($myrow['ratecenter']), htmlspecialchars($myrow['carrierid']), htmlspecialchars($myrow['errormessage'])); 
        } 
        ?> 

        </table>
	<br/>
	<br/>
	<input type ="button" onclick="MoveHELDCDR()" value="Move these calls back to the rating queue" />
	<br/>
	<br/> 
	<a href="main.php"><--Back</a>
    </body> 
</html>