
<?php
include 'lib/Page.php';
$bandwidthMap = array();
$bandwidthMap['DEBTOR_ID'] = 'debtor_id';
$bandwidthMap['item.id'] = 'itemid';
$bandwidthMap['computed.billable_minutes'] = 'billable_minutes';
$bandwidthMap['item.type'] = 'itemtype';
$bandwidthMap['computed.trans_rate'] = 'trans_rate';
$bandwidthMap['AMOUNT'] = 'amount';
$bandwidthMap['RECORD_DATE'] = 'record_date';
$bandwidthMap['item.src'] = 'src';
$bandwidthMap['item.dest'] = 'dest';
$bandwidthMap['item.dst_rcs'] = 'dst_rcs';
$bandwidthMap['lrn'] = 'lrn';

$vitelityMap = array();
$vitelityMap['Date'] = 'calldatetime';
$vitelityMap['Source'] = 'source';
$vitelityMap['Destination'] = 'destination';
$vitelityMap['Seconds'] = 'seconds';
$vitelityMap['CallerID'] = 'callerid';
$vitelityMap['Disposition'] = 'disposition';
$vitelityMap['Cost'] = 'cost';

$thinktelMap = array();
$thinktelMap['"Source Number"'] = 'sourcenumber';
$thinktelMap['"Destination Number"'] = 'destinationnumber';
$thinktelMap['"Call Date"'] = 'calldate';
$thinktelMap['"Usage Type"'] = 'usagetype';
$thinktelMap['"Call Duration (Seconds)"'] = 'rawduration';
?>


<?php
include 'lib/TBRLibs.php';
include 'config.php';

$content = '';
if(isset($_POST['loadImport'])){
	
	$myFile = $_FILES['uploadedFile']['tmp_name'];
	$type = $_POST['type'];
	
	if($myFile == ""){
		if($_FILES['uploadedFile']['error'] == 1){
			$content .= "<font color=\"red\">Error importing file</font><br>";
		}
		else{
			$content .= "<font color=\"red\">Please choose a file.</font><br>";
		}
		
	}
	else{
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		echo "Please wait <br>";
		echo '<blink>Working</blink>';
		
		echo '<form name="myForm" enctype="multipart/form-data" action="listresultstbr.php" method="POST">';
		echo '<input type="hidden" name="type" value="'.$type.'"/>';
		if($type == 'telastic'){
			echo '<input type="hidden" name="importTelastic" value="1"/>';
		}
		else if($type == 'sansay'){
			echo '<input type="hidden" name="importSansay" value="1"/>';
		}
		else if($type == 'nextone'){
			echo '<input type="hidden" name="importNextone" value="1"/>';
		}
		else if($type == 'netsapiens'){
			echo '<input type="hidden" name="importNetSapiens" value="1"/>';
		}
		else if($type == 'telepo'){
			echo '<input type="hidden" name="importTelepo" value="1"/>';
		}
		else if($type == 'aretta'){
			echo '<input type="hidden" name="importAretta" value="1"/>';
		}
		else if($type == 'slinger'){
			echo '<input type="hidden" name="importSlinger" value="1"/>';
		}
		else if($type == 'cisco'){
			echo '<input type="hidden" name="importCisco" value="1"/>';
		}
		else if($type == 'voip'){
			echo '<input type="hidden" name="importVoip" value="1"/>';
		}
		
		else{
			echo '<input type="hidden" name="import" value="1"/>';
		}
		echo '<input type="hidden" name="data" value="'.htmlspecialchars($theData).'"/>';
		echo '</form>';
		
		echo '<script type="text/javascript">';
		echo '	document.myForm.submit();';
		echo '</script>';
		echo '<!--';
	}
}
else if(isset($_POST['import'])){
	include 'config.php';
	$theData = $_POST['data'];
	$delete = false;
	$primKeys = array();
	$headerMap = array();
	$type = $_POST['type'];
	$delim = '/,/';
	$table = '';
	$vitelityException = False;
	$callidFields = array();
	$numbersToInternationalize = array();
	$toSkipOnZero = array();
	$fieldsToSkip = array();
	
	if($type == 'bandwidth'){
		$callidFields[] = 'itemid';
		$callidFormat = "itemid";
		$table = 'bandwidthcdr';
		$moveStatement = "SELECT \"fnMoveBandwidthCDRToTBR\"();";
		$delim = '\|';
		$headerMap = $bandwidthMap;
	}
	else if($type == 'vitelity'){
		$toSkipOnZero[] = 'seconds';
		$callidFields[] = 'calldatetime';
		$callidFields[] = 'source';
		$callidFields[] = 'destination';
		$callidFields[] = 'seconds';
		
		$callidFormat = "calldatetime || '_' || source || '_' || destination || '_' || seconds";
		
		$numbersToInternationalize[] = 'source';
		$numbersToInternationalize[] = 'destination';
		
		$fieldsToSkip[] = 'cost';
		$table = 'vitelitycdr';
		$moveStatement = "SELECT \"fnMoveVitelityCDRToTBR\"();";
		$delim = ',';
		$headerMap = $vitelityMap;
	}
	else if($type == 'thinktel'){
		$toSkipOnZero[] = 'rawduration';
		$numbersToInternationalize[] = 'sourcenumber';
		$numbersToInternationalize[] = 'destinationnumber';
		
		$callidFields[] = 'calldate';
		$callidFields[] = 'sourcenumber';
		$callidFields[] = 'destinationnumber';
		$callidFields[] = 'rawduration';
		$callidFormat = "calldate || '_' || sourcenumber || '_' || destinationnumber || '_' || rawduration";
		$table = 'thinktelcdr';
		$moveStatement = 'SELECT "fnMoveThinktelCDRToTBR"();';
		$delim = ',';
		$headerMap = $thinktelMap;
	}
	
	$keyedData = TurnCSVIntoAssocArray($theData, $delim, $headerMap);
	if(count($keyedData == 0)){
		
	}
	#begin connecting to the database
	$db = pg_connect($connectstring);
	set_time_limit(0);
	
	#initialize statistics we're going to keep track of
	$lineCount = 2;
	$duplicateRows = array();
	$insertedItemCount = 0;
	$voidedCalls = 0;
	foreach($keyedData as $row){
		$values = array();
		
		#skip any rows that are missed or unconnected calls
		foreach($toSkipOnZero as $fieldToCheck){
			if($row[$fieldToCheck] == 0){
				$voidedCalls++;
				continue 2;
			}
		}
		
		#some data contain phone numbers that need to have + or 1 or +1 added to them
		foreach($numbersToInternationalize as $number){
			$row[$number] = InternationalizePhoneNumber($row[$number]);
		}
		
		#build an array that contains "$col = $value" strings to add to various queries later
		$columnEqualValue = array();
		foreach($row as $key =>$val){
			$values[] = "'".$val."'";
			foreach($fieldsToSkip as $field){
				if($key == $field){
					continue 2;
				}
			}
			$columnEqualValue[] = $key .= " = " ."'".$val."'";
		}
		
		$rowNames = array_keys($row);
		
		#insertquery
		$insertStatement = 
		"INSERT 
		INTO 	".$table."(".implode($rowNames,',').")
		SELECT	".implode($values,",")."
		WHERE NOT EXISTS
		(SELECT 1 FROM ".$table." 
		WHERE 
		".implode($columnEqualValue,' AND ')."
		)
		RETURNING ".$callidFormat." as callid;";
		
		$insertResults = pg_query($insertStatement);
		$callidArray = pg_fetch_all($insertResults);
		
		if(count($callidArray[0]) > 0){
			$callid =  $callidArray[0]['callid'];
		}
		else{
			$duplicateRows[] = $lineCount;
			$lineCount++;
			continue;
		}
		#check to see if the record already exists in the callrecordmaster
		$checkTBRStatement = "SELECT callid FROM callrecordmaster_tbr WHERE callid = '" .$callid. "';";
		$checkResult = pg_query($checkTBRStatement);
		$checkArray = pg_fetch_all($checkResult);
		if(count($checkArray[0]) == 1){
			#delete statement
			$deleteStatement = "DELETE FROM ".$table." WHERE ".implode($columnEqualValue,' AND ').";";
			#delete any instance of the row in the company database (not master)
			pg_query($deleteStatement);
			$duplicateRows[] = $lineCount;
			$lineCount++;
			continue;
		}
		$insertedItemCount++;
		$lineCount++;
	}
	
	if(isset($moveStatement)){
		pg_query($moveStatement);
	}
	
	$content = '';
	$duplicateRowCount = count($duplicateRows);
	if(count($duplicateRows) > 0){
		$content .= <<<HEREDOC
		WARNING : {$duplicateRowCount} duplicate entries found on lines :
HEREDOC;
		$content .= implode($duplicateRows, ', ');
		$content .= '<p>';
	}
	if($voidedCalls >0){
		$content .= $voidedCalls . " rows skipped for zero duration.<br>";
	}
	$content .= $insertedItemCount . " items inserted.<br>";
	$content .= <<<HEREDOC
	<a href="main.php"><--Back</a>
	<br/>
	<br/>
HEREDOC;
	echo $content;
	echo '<!--';
	pg_close($db);
	
}
		/*
		$assocItem = array();
		$assocItem['callid'] = $item[];
		$assocItem['customerid'] = $item[];
		$assocItem['calltype'] = $item[];
		$assocItem['calldatetime'] = $item[];
		$assocItem['duration'] = $item[];
		
		$assocItem['direction'] = $item[];
		$assocItem['sourceip'] = $item[];
		$assocItem['originatingnumber'] = $item[];
		$assocItem['destinationnumber'] = $item[];
		$assocItem['lrn'] = $item[];
		
		$assocItem['cnamdipped'] = $item[];
		$assocItem['ratecenter'] = $item[];
		$assocItem['carrierid'] = $item[];
		$assocItem['wholesalerate'] = $item[];
		$assocItem['wholesaleprice'] = $item[];
		
	   */
else if(isset($_POST['importTelastic'])){
	$theData = $_POST['data'];
	$content .= ProcessTelastic($theData, $connectstring);
}
else if(isset($_POST['importSansay'])){
	$theData = $_POST['data'];
	$content .= ProcessSansay($theData, $connectstring);
}
else if(isset($_POST['importNextone'])){
	$theData = $_POST['data'];
	$content .= ProcessNextOne($theData, $connectstring);
}
else if(isset($_POST['importNetSapiens'])){
	$theData = $_POST['data'];
	
	$content .= ProcessNetSapiens($theData, $connectstring);
}
else if(isset($_POST['importTelepo'])){
	$theData = $_POST['data'];
	
	$content .= ProcessTelepo($theData, $connectstring);
}
else if(isset($_POST['importAretta'])){
	$theData = $_POST['data'];
	$content .= ProcessAretta($theData, $connectstring);
}
else if(isset($_POST['importSlinger'])){
	$theData = $_POST['data'];
	
	$content .= ProcessSlinger($theData, $connectstring);
}
else if(isset($_POST['importCisco'])){
	$theData = $_POST['data'];
	
	$content .= ProcessCisco($theData, $connectstring);
}
else if(isset($_POST['importVoip'])){
	$theData = $_POST['data'];
	
	$content .= ProcessVOIP($theData, $connectstring);
}
else if(isset($_POST['dirtsimple'])){
	$theData = $_POST['data'];
	
	$content .= ProcessSimple($theData, $connectstring);
}

?>
<?php
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';

	$query = 'SELECT * FROM "callrecordmaster_tbr" where calltype is not null';

	$titlespiped = "CallID,CustomerID,CallType,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDipped,RateCenter,CarrierID";
	$titles = preg_split("/,/",$titlespiped,-1);
	$queryResult = SQLSelectQuery($connectstring, $query, ",", "\n");
	$htmltable = QueryResultToTable($queryResult, ",",$titles); 
?> 

		<?php echo GetPageHead("List Results TBR", "main.php");?>
		
		<div id="body">
			<?php echo $content;?>
			<form name="export" action="exportpipe.php" method="post">
				<input type="submit" class="btn orange export" value="Export Table">
				<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
				<input type="hidden" name="filename" value="TBRExport.csv">
			</form>
			
			<form enctype="multipart/form-data" action="listresultstbr.php" method="post">
				<input type="hidden" name="loadImport" value="1"/>
				<select name="type">
					<option value="bandwidth">Bandwidth</option>
					<option value="vitelity">Vitelity</option>
					<option value="thinktel">Thinktel</option>
					<option value="telastic">Telastic</option>
					<option value="sansay">Sansay</option>
					<option value="nextone">Nextone</option>
					<option value="netsapiens">NetSapiens</option>
					<option value="telepo">Telepo</option>
					<option value="aretta">Aretta</option>
					<option value="slinger">Slinger</option>
					<option value="cisco">Cisco Call Manager 7.1</option>
					<option value="voip">VOIP Innovations</option>
				</select>
				<input name="uploadedFile" type="File" />
				<input type="submit" value="Import File"/>
			</form>
			<?php echo $htmltable; ?>
	
		</div>
	
	<?php echo GetPageFoot();?>