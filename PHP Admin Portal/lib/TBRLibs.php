<?php
$debug = false;
function print_debug($debugString){
	global $debug;
	if($debug){
		echo $debugString. '<br>';
	}
}

function TurnCSVIntoAssocArray($theData, $delim, $carriageReturn = "\n"){
		print_debug('Entering CSV data into array');
		$allRows = preg_split('/\n/', $theData, -1);
		print_debug('Data split into rows - number of rows: ' . count($allRows));
		$regexp = '/'.$delim.'?("[^"]*"|[^'.$delim.']*)/';
		$regExResults = array();
		#get all the keys from the first row
		$keys = preg_match_all($regexp, trim($allRows[0]), $regExResults);
		$keys = $regExResults[1];
		print_debug('Reading keys - number of keys: '. count($keys));
		
		#build a mega array of the rows containing the row columns as the key
		$keyedData = array();
		for($j = 1; $j < count($allRows); $j++){
			$regExResults = array();
			$row = $allRows[$j];
			$row = trim($row);
			$row = preg_match_all($regexp, $allRows[$j], $regExResults);
			$row = $regExResults[1];

			$rowDataWithHeaders = array();
			
			if(count($row) == 0){
				print_debug('WARNING - Invalid row ');
				print_debug('Item count: '. count($row));
				print_debug($allRows[$j]);
				continue;
			}
			
			for($i = 0; $i < count($keys); $i++){
				$key = $keys[$i];
				if(empty($key)){
					continue;
				}
				if(count($row) <= $i){
					print_debug('Invalid row: '. $allRows[$j]);
					break;
				}
				$val = $row[$i];
				$rowDataWithHeaders[$key] = $val;
			}
			$keyedData[] = $rowDataWithHeaders;
		}
		print_debug(count($keyedData). ' valid rows entered.');
		return $keyedData;
}
function InternationalizePhoneNumber($phoneNumber){
	if(strlen($phoneNumber) == 10 and substr($phoneNumber,0,1) != '+'){
		$phoneNumber = '+1'.$phoneNumber;
	}
	if(strlen($phoneNumber) == 11 and substr($phoneNumber,0,1) == '1'){
		$phoneNumber = '+1'.$phoneNumber;
	}
	if(substr($phoneNumber,0,3) == '011'){
		$phoneNumber = '+'.substr($phoneNumber,3,20);
	}
	return $phoneNumber;
}
function InsertDataIntoCallRecordMaster($keyedData, $connectstring){
	print_debug('Inserting data into call record master - number of rows : '. count($keyedData));
	$table = 'callrecordmaster_tbr';

	$duplicateRows = array();
	$insertedItemCount = 0;
	$lineCount = 2;
	
	#begin connecting to the database
	$db = pg_connect($connectstring);
	set_time_limit(0);
	
	foreach($keyedData as $row){
		$checkStatement = "SELECT 1 FROM ".$table." 
				WHERE callid = '". $row['callid']."';";
		$checkResults = pg_query($checkStatement);
		$checkArray = pg_fetch_all($checkResults);
		
		if(count($checkArray[0]) > 0){
			print_debug('Duplicate row found : ' .$row['callid']);
			$duplicateRows[] = $lineCount;
			$lineCount++;
			continue;
		}
		
		#build an array that contains "$col = $value" strings to add to various queries later
		$columnEqualValue = array();
		$values = array();
		foreach($row as $key =>$val){
			$values[] = "'".$val."'";
			$columnEqualValue[] = $key .= " = " ."'".$val."'";
		}
		$rowNames = array_keys($row);
		$insertStatement = "INSERT 
				INTO 	".$table."(".implode($rowNames,',').")
				SELECT	".implode($values,",")."
				RETURNING 1 as callid;";
		
		$insertResults = pg_query($insertStatement);
		$callidArray = pg_fetch_all($insertResults);
		
		$callid =  $callidArray[0]['callid'];
		
		$lineCount++;
		$insertedItemCount++;
	}
	
	pg_close($db);
	
	$ret = array();
	$ret['DuplicateRows'] = $duplicateRows;
	$ret['InsertedItemCount'] = $insertedItemCount;
	return $ret;

}
function PrintResults($results){
	$duplicateRows = $results['DuplicateRows'];
	$insertedItemCount = $results['InsertedItemCount'];
	$voidedCalls = $results['VoidedCalls'];
	
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
	return $content;
}


function ProcessTelastic($theData, $connectstring){
	$delim = ',';
	
	$voidedCalls = 0;
	
	$rawKeyedData = TurnCSVIntoAssocArray($theData, $delim);
	
	$keyedData = array();
	foreach($rawKeyedData as $row){
		
		if($row['TotalSeconds'] == "0"){
			$voidedCalls++;
			continue;
		}
		$item['callid'] = $row['AnswerTime'] 
							. '_' . $row['CallingNumber'] 
							. '_' . $row['CalledNumber']
							. '_' . $row['TotalSeconds'];
		$item['calldatetime']  = $row['AnswerTime'];
		$item['duration'] = $row['TotalSeconds'];
		if($row['Direction'] == "OUTBOUND"){
			$item['direction'] = "O";
		}
		else{
			$item['direction'] = "I";
		}
		$item['originatingnumber'] = $row['CallingNumber'];
		$item['destinationnumber'] = $row['CalledNumber'];
		$item['cnamdipped'] = 'f';
		$item['carrierid'] = "TELASTIC";
		$keyedData[] = $item;
	}
	
	$insertResults = InsertDataIntoCallRecordMaster($keyedData, $connectstring);
	
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessSansay($theData, $connectstring){
	$delim = ';';
	
	$voidedCalls = 0;
	
	$allRows = preg_split('/\r\n/', $theData, -1);
	$keyedData = array();
	foreach($allRows as $row){
		$item = preg_split('/'.$delim.'/',$row,-1);
		if($row == '' or !isset($item[1])){
			continue;
		}
		$assocItem = array();
		$assocItem['originatingnumber'] = $item[15];
		$assocItem['destinationnumber'] = $item[17];
		$assocItem['calldatetime'] = $item[6];
		$assocItem['duration'] = $item[54];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = "";
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];

		$keyedData[] = $assocItem;
	}
	
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessNextOne($theData, $connectstring){
	$delim = ';';
	
	$voidedCalls = 0;
	
	$allRows = preg_split('/\r\n/', $theData, -1);
	$keyedData = array();
	
	foreach($allRows as $row){
		$item = preg_split('/'.$delim.'/',$row,-1);
		if($row == '' or !isset($item[1])){
			continue;
		}
		$assocItem = array();
		$assocItem['originatingnumber'] = $item[17];
		$assocItem['destinationnumber'] = $item[9];
		$assocItem['sourceip'] = $item[3];
		$assocItem['calldatetime'] = $item[0];
		$assocItem['duration'] = $item[35];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = $item[6];
		$assocItem['callid'] = $item[23];
		if($assocItem['duration'] == 0){
			$voidedCalls++;
			continue;
		}
		$keyedData[] = $assocItem;
	}
	
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessNetSapiens($theData, $connectstring){
	$delim = ',';
	
	$voidedCalls = 0;
	
	$allRows = preg_split('/\r\n/', $theData, -1);
	$keyedData = array();
	
	foreach($allRows as $row){
		$item = preg_split('/'.$delim.'/',$row,-1);
		if($row == '' or !isset($item[1])){
			continue;
		}
		$assocItem = array();
		/*
		If fields(2) = "" Then 'orig_sub -- inbound call
			If (fields(5) = "") Then
				sCustomerID = fields(4)
			Else   
				sCustomerID = fields(5) ' 4-by_domain    5-term_domain
		Else  'outbound call
			If (fields(3) = "") Then 
				sCustomerID = fields(4)
			Else 
				sCustomerID = fields(3) ' 3-orig_domain   4-by_domain   
		End If
		*/
		if(empty($item[2])){
			if(empty($item[5])){
				$assocItem['customerid'] = $item[4];
			}
			else{
				$assocItem['customerid'] = $item[5];
			}
		}
		else{
			if(empty($item[3])){
				$assocItem['customerid'] = $item[4];
			}
			else{
				$assocItem['customerid'] = $item[3];
			}
		}
		$assocItem['calldatetime'] = $item[0];
		$assocItem['duration'] = $item[1];
		
		$assocItem['originatingnumber'] = $item[6];
		$assocItem['destinationnumber'] = $item[7];
		
		$assocItem['cnamdipped'] = 'f';
		
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
								
		if($assocItem['duration'] == 0){
			$voidedCalls++;
			continue;
		}
		$keyedData[] = $assocItem;
	}
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessTelepo($theData, $connectstring){
	$delim = ',';
	
	$voidedCalls = 0;
	
	$allRows = preg_split('/\r\n/', $theData, -1);
	$keyedData = array();
	foreach($allRows as $row){
		$item = preg_split('/'.$delim.'/',$row,-1);
		if($row == '' or !isset($item[1])){
			continue;
		}
		/*
		0	orgid
		1	customerid
		2	billinguserid
		3	billingphonenumber
		4	billingcostcenter
		5	billingdepartment
		6	billingrole
		7	starttime
		8	calltype
		9	callid
		10	initialcallid
		11	confirmeddurationseconds
		12	waitbeforeanswerseconds
		13	result
		14	sourceuserid
		15	sourcephonenumber
		16	sourcecostcenter
		17	sourcedepartment
		18	sourcerole
		19	sourceaptype
		20	targetuserid
		21	targetphonenumber
		22	...
		*/
		$assocItem = array();
		$assocItem['customerid'] = $item[2];
		$assocItem['calldatetime'] = $item[7];
		$assocItem['duration'] = $item[11];
		if($assocItem['duration'] == 0){
			$voidedCalls++;
			continue;
		}
		
		if($item[8] == '1'){
			$assocItem['direction'] = 'O';
		}
		else if($item[8] == '2'){
			$assocItem['direction'] = 'I';
		}
		$assocItem['originatingnumber'] = $item[15];
		$assocItem['destinationnumber'] = $item[21];
		
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = $item[17];
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		$keyedData[] = $assocItem;
	}
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessAretta($theData, $connectstring){
	$delim = ',';
	
	$voidedCalls = 0;
	
	$allRows = preg_split('/\r\n/', $theData, -1);
	$keyedData = array();
	
	foreach($allRows as $row){
		$item = preg_split('/'.$delim.'/',$row,-1);
		if($row == '' or !isset($item[1])){
			continue;
		}
		
		$assocItem = array();
		$assocItem['originatingnumber'] = $item[3];
		$assocItem['destinationnumber'] = $item[4];
		$assocItem['sourceip'] = '';
		$assocItem['calldatetime'] = $item[0];
		$assocItem['duration'] = $item[5];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['carrierid'] = 'ARETTA';
		$assocItem['wholesaleprice'] = $item[7];
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		
		if($item[2] == "Outbound"){
			$assocItem['direction'] = "O";
			$assocItem['calltype'] = '35';
		}
		else{
			$assocItem['direction'] = "I";
			$assocItem['calltype'] = '15';
		}
		if($assocItem['duration'] == 0){
			$voidedCalls++;
			continue;
		}
		$keyedData[] = $assocItem;
	}
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	return $finalResult;
}
function ProcessSlinger($theData, $connectstring){
	$delim = ',';
	$voidedCalls = 0;
	
	$rawKeyedData = TurnCSVIntoAssocArray($theData, $delim);
	
	$keyedData = array();
	foreach($rawKeyedData as $row){
		if($row['Duration'] == "\"0\""){
			$voidedCalls++;
			continue;
		}
		$item['callid'] = str_replace('"', "", $row['CallID']);
		$item['calldatetime']  = str_replace('"', "", $row['CallDateTime']);
		$item['duration'] = number_format(str_replace('"', "", $row['Duration']),0,'','');
		$item['originatingnumber'] = str_replace('"', "", $row['OriginatingNumber']);
		$item['destinationnumber'] = str_replace('"', "", $row['DestinationNumber']);
		if(str_replace('"', "", $row['CNAMDipped']) == ""){
			$item['cnamdipped'] = 'f';
		}
		else{
			$item['cnamdipped'] = str_replace('"', "", $row['CNAMDipped']);
		}
		$item['carrierid'] = str_replace('"', "", $row['CarrierID']);
		$item['sourceip'] = str_replace('"', "", $row['SourceIP']);
		$keyedData[] = $item;
	}
	
	$insertResults = InsertDataIntoCallRecordMaster($keyedData,$connectstring);
	
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	
	return $finalResult;
}
function ProcessCisco($theData, $connectString){
	date_default_timezone_set('America/New_York');
	$delim = ',';
	
	$voidedCalls = 0;
	
	$rawKeyedData = TurnCSVIntoAssocArray($theData, $delim, '\n');

	$keyedData = array();
	$j = 1;
	foreach($rawKeyedData as $row){
		if($j==1){
			$j++;
			continue;
		}
		if($row['"duration"'] == "0"){
			$voidedCalls++;
			continue;
		}
		$assocItem = array();
		$assocItem['calldatetime'] = date('Y-m-d H:i:s', $row['"dateTimeOrigination"']);
		$assocItem['duration'] = $row['"duration"'];
		
		$assocItem['originatingnumber'] = str_replace('"', "", $row['"callingPartyNumber"']);
		$assocItem['destinationnumber'] = str_replace('"', "", $row['"finalCalledPartyNumber"']);
		
		$assocItem['carrierid'] = str_replace('"', "", $row['"destIpv4v6Addr"']);
		$assocItem['sourceip'] = str_replace('"', "",$row['"origIpv4v6Addr"']);
		$assocItem['cnamdipped'] = 'f';
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		$keyedData[] = $assocItem;
		$j++;
	}
	$finalResult = '';
	$insertResults = InsertDataIntoCallRecordMaster($keyedData, $connectString);
	
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	return $finalResult;
}
function ProcessVOIP($theData, $connectString){
	print_debug('Processing VOIP file');
	$delim = ';';
	
	$voidedCalls = 0;
	
	$rawKeyedData = TurnCSVIntoAssocArray($theData, $delim, '\n');
	print_debug("Keys : ");
	print_debug(implode(array_keys($rawKeyedData[0]), ","));
	$keyedData = array();
	foreach($rawKeyedData as $row){
	/*CallType,StartTime,StopTime,CallDuration,BillDuration,CallMinimum,CallIncrement,BasePrice,CallPrice,
	TransactionId,CustomerIP,ANI,ANIState,DNIS,LRN,DNISState,DNISLATA,DNISOCNOrig,Tier*/
		$assocItem = array();
		switch ($row['CallType']){
			case 'TERM_EXT_US_INTER':
				$assocItem['calltype'] = 25;
			case '800OrigC':
				$assocItem['calltype'] = 30;
			case '800OrigE':
				$assocItem['calltype'] = 30;
			case 'Orig-Tiered':
				$assocItem['calltype'] = 15;
			case 'TERM_INTERSTATE':
				$assocItem['calltype'] = 10;
			case 'TERM_INTRASTATE':
				$assocItem['calltype'] = 5;
		}
		$assocItem['calldatetime'] = $row['StartTime'];
		$assocItem['duration'] = intval($row['CallDuration']);
		
		$assocItem['originatingnumber'] = $row['ANI'];
		$assocItem['destinationnumber'] = $row['DNIS'];
		
		$assocItem['lrn'] = $row['LRN'];
		$assocItem['ratecenter'] = $row['Tier'];
		$assocItem['carrierid'] = 'VI';
		$assocItem['sourceip'] = $row['CustomerIP'];
		$assocItem['cnamdipped'] = 'f';
		$assocItem['callid'] = $assocItem['calldatetime'] 
								. '_' . $assocItem['originatingnumber']
								. '_' . $assocItem['destinationnumber']
								. '_' . $assocItem['duration'];
		if($assocItem['duration'] == '0'){
			$voidedCalls++;
			continue;
		}
		$keyedData[] = $assocItem;
	}
	$finalResult = '';
	$insertResults = InsertDataIntoCallRecordMaster($keyedData, $connectString);
	
	$insertResults['VoidedCalls'] = $voidedCalls;
	$finalResult = PrintResults($insertResults);
	return $finalResult;
}
?>