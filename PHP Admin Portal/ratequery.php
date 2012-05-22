<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	
	$content = '';
	$db = pg_connect($connectstring);
	if(isset($_GET["load"])){
		$configPage = $_GET["confpage"];
		$customerid = $_GET["customerid"];
		$content .= "Please wait <br>";
		$content .= "<blink>Working</blink>";
		$content .= '<form name="myForm" enctype="multipart/form-data" action="ratequery.php" method="POST">';
		$content .= '<input type="hidden" name="configPage" value="' . $configPage . '"/>';
		$content .= '<input type="hidden" name="customerid" value="'.$customerid.'"/>';
		$content .= '<input type="hidden" name="query" value="1"/>';
		$content .= '</form>';
		
		$content .= '<script type="text/javascript">';
		$content .= 'document.myForm.submit();';
		$content .= '</script>';
	}
	else if(isset($_POST["loadexport"])){
		$query = $_POST["queryString"];
		$customerid = $_POST["customerid"];
		$filepath = "files/".$customerid.".csv";
		
		$content .= "Please Wait <br>";
		$content .= "<blink>Working</blink>";
		
		$content .= '<form name="myForm" enctype="multipart/form-data" action="ratequery.php" method="POST"/>';
		$content .= '<input type="hidden" name="queryString" value="'.htmlspecialchars($query).'">';
		$content .= '<input type="hidden" name="customerid" value="'.$customerid.'">';
		$content .= '<input type="hidden" name="export" value="1"/>';
		$content .= '</form>';
		
		$content .= '<script type="text/javascript">';
		$content .= '	document.myForm.submit();';
		$content .= '</script>';
	}
	else if(isset($_POST["export"])){
		include_once $path . 'lib/SQLQueryFuncs.php';
		$query = $_POST["queryString"];
		$customerid = $_POST["customerid"];
		$filepath = "files/".$customerid.".csv";
		
		$queryResult = SQLSelectQuery($connectstring, $query, ",", "\r\n"); 		
		SaveQueryResultsToCSV($connectstring, $queryResult, $filepath);
		
		$content .= 'Done<br>';
		$content .= '<a href="javascript:history.go(-2)">Back to query</a>';
		
		$content .= '<script type="text/javascript">';
		$content .=	'window.location = "'.$filepath.'"';
		$content .= '</script>';
	}
	else if(isset($_POST["loadimport"])){
		include $_POST["configPage"];
		$content = '';
		$myFile = $_FILES['uploadedFile']['tmp_name'];
		if($myFile == ""){
			$content .= "Invalid File";
		}
		else{
			$fh = fopen($myFile, 'r');
			$theData = fread($fh, filesize($myFile));
			fclose($fh);
			$content .= "Please wait <br>";
			$content .= '<blink>Working</blink>';
		}
		$customerid = $_POST["customerid"];
		$content .= '<form name="myForm" enctype="multipart/form-data" action="ratequery.php" method="POST">';
		$content .= '<input type="hidden" name="import" value="1"/>';
		$content .= '<input type="hidden" name="configPage" value="'.htmlspecialchars($_POST["configPage"]).'">';
		$content .= '<input type="hidden" name="customerid" value="'.$customerid.'">';
		$content .= '<input type="hidden" name="data" value="'.htmlspecialchars($theData).'"/>';
		$content .= '</form>';
		
		$content .= '<script type="text/javascript">';
		$content .= '	document.myForm.submit();';
		$content .= '</script>';
	}
	else if(isset($_POST["import"])){
		include_once $path . 'config.php';
		include_once $path . 'lib/SQLImport.php';
		include $_POST["configPage"];
		
		$customerNumberField = 'customerid';
		$theData = $_POST["data"];
			
		# DELETE
		$deleteStatement = "DELETE FROM " . $table 
			. " WHERE ";
		$count = 1;
		$primKeys = preg_split('/,/', $primaryKeys, -1);
		foreach($primKeys as $key){
			if($count != 1){
				$deleteStatement .= " AND ";
			}
			$deleteStatement .= '"' . $key . "\" = " . '$'.$count . " ";
			$count += 1;
		}
		$deleteStatement .= ';';
		# INSERT
		$allColArray = preg_split('/,/', $allColumns, -1);
		$insertStatement = "INSERT INTO " . $table . "(";
			$count = 1;
			foreach($allColArray as $col){
			if($count != 1){
				$insertStatement .= ",";
			}
			$insertStatement .= '"' . $col . "\"";
			$count += 1;
		}
		$insertStatement .= ")";
		$insertStatement .= " VALUES (";
		for($i = 1; $i <= count($allColArray); $i++){
			if($i!=1){
				$insertStatement .= ',';
			}
			$insertStatement .= '$' . $i;
		}
		$insertStatement .= ");";
		set_time_limit(0);
		$result = pg_prepare($db, "delete", $deleteStatement);
		$result = pg_prepare($db, "insert", $insertStatement);
			
		$allRows = preg_split('/\r\n/', $theData, -1);		
		$count = 1;
		foreach($allRows as $row){
			if($row == ""){
				continue;
			}
			if($count == 1){
				$headers = preg_split('/,/', $row, -1);
				$count += 1;
				continue;
			}
			$vals = preg_split('/,/', $row, -1);

			$arr[$customerNumberField] = $_POST["customerid"];
			foreach($vals as $key => $value){
				$arr[$headers[$key]] = $value;
			}
			$deleteParams;
			$i = 0;
			foreach($primKeys as $key){
				$deleteParams[$i] = $arr[$key];
				$i++;
			}
				
			$insertParams;
			$i = 0;
			foreach($allColArray as $key){
				$insertParams[$i] = $arr[$key];
				$i++;
			}
			$result = pg_execute($db, "delete", $deleteParams);
			$result = pg_execute($db, "insert", $insertParams);
			$count += 1;
		}
		
		$content = 'Task Complete<br>';
		$content .= '<a href="javascript:history.go(-3)">Back to query</a>';
	}
	else if(isset($_POST["query"])){
		include_once $path . 'lib/SQLQueryFuncs.php';
		include_once $path . 'config.php';
		include $_POST["configPage"];
		
		$customerNumberField = 'customerid';
		$customerid = $_POST["customerid"];
		
        $queryNumberofRows = 'SELECT count(*) FROM '.$table.' WHERE "customerid" = \''.$customerid.'\';';
		$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
		$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
		$numberOfRows = $numberOfRowsArray[0];
		$configPage = $_POST["configPage"];
		$offset = 0;
		if(isset($_POST["offset"])){
			$offset = $_POST["offset"];
		}
		$limit = 10000;
		$endoffset = min($offset + $limit, $numberOfRows);
		$prevoffset = max($offset - $limit, 0);


		$fullQuery = "SELECT " . $relevantColumns . " FROM " . $table 
			. " WHERE " . $customerNumberField . " = '" . $customerid . "'"
			. " ORDER BY " . $orderByColumns;
		$limitedQuery = $fullQuery
			. " LIMIT "
			. $limit
			. " OFFSET "
			. $offset	
			. ";";

		$queryResult = SQLSelectQuery($connectstring, $limitedQuery, ",", "\n");
		$content = <<< HEREDOC
		A valid file is a csv with the following as the first line: <br><br>
		{$relevantColumns}<p><br>
		Every subsequent line will have the appropriate data.<br>
		Note that there are <b>no surrounding quotes anywhere</b> and <b>all column names are case sensitive</b>.<br>
HEREDOC;
		$content .= '
			
		<!-- THE EXPORT BUTTON -->
		<form action="ratequery.php" method="post">
		<input type="hidden" name="loadexport" value="1"/>
		<input type="hidden" name="queryString" value="'.htmlspecialchars($fullQuery).'" />
		<input type="hidden" name="customerid" value="'.htmlspecialchars($customerid).'" />
		<input type="submit" class="btn orange export" value="Export to CSV"/>
		</form>

		<!-- THE IMPORT BUTTON -->
		<form enctype="multipart/form-data" action="ratequery.php" method="POST">
		<input type="hidden" name="loadimport" value="1"/>
		Choose a file to import: <input name="uploadedFile" type="File" />
		<input type="hidden" name="configPage" value="'.htmlspecialchars($configPage).'">
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="submit" value="import File" />
		</form>

		Showing rows : '.$offset . ' to ' . $endoffset.' <br>
		Total number of rows : '.$numberOfRows.'
		<br>';
		
		if($offset > 0){
			$content .= '
			<form action="ratequery.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
			<input type="hidden" name="configPage" value="'.htmlspecialchars($configPage).'">
			<input type="hidden" name="customerid" value="'.$customerid.'">
			<input type="hidden" name="offset" value="'.$prevoffset.'">
			<input type="hidden" name="query" value="1"/>
			<input type="submit" value="View prev '.$limit.' results"/>
			</form>';
		}
		if($endoffset < $numberOfRows){
		$content .= '
		<form action="ratequery.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="configPage" value="'.htmlspecialchars($configPage).'">
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="hidden" name="offset" value="'.$endoffset.'">
		<input type="hidden" name="query" value="1"/>
		<input type="submit" value="View next '.$limit.' results"/>
		</form>';
		}
		$content .= QueryResultToTable($queryResult, ",",explode(",",$relevantColumns));
	}
?>

	<?php echo GetPageHead("View Customer Rates");?>
	<div id="body">
	<?php echo $content;?>
	</div>
	<?php echo GetPageFoot();?>
