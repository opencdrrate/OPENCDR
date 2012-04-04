<?php
function SQLImport($connectString, $schema, $table, 
		$primKeys, $columnTitles, &$theData){
	$db = pg_connect($connectString);
	# DELETE
	$deleteStatement = "DELETE FROM ". $table
		. " WHERE ";
	$count = 1;
	foreach($primKeys as $key){
		if($count != 1){
			$deleteStatement .= " AND ";
		}
		$deleteStatement .= '"' . $key . "\" = " . '$'.$count . " ";
		$count += 1;
	}
	$deleteStatement .= ';';
	# INSERT
	$insertStatement = "INSERT INTO ". $table . "(";
		$count = 1;
		foreach($columnTitles as $col){
		if($count != 1){
			$insertStatement .= ",";
		}
		$insertStatement .= '"' . $col . "\"";
		$count += 1;
	}
	$insertStatement .= ")";
	$insertStatement .= " VALUES (";
	for($i = 1; $i <= count($columnTitles); $i++){
		if($i!=1){
			$insertStatement .= ',';
		}
		$insertStatement .= '$' . $i;
	}
	$insertStatement .= ");";
	
	set_time_limit(0);
	$result = pg_prepare($db, "delete", $deleteStatement);
	$result = pg_prepare($db, "insert", $insertStatement);
		
	$allRows = preg_split('/\r\n|\r|\n/', $theData, -1);		
	$count = 1;
	foreach($allRows as $row){
		$delim = ",";
		$regExp = '/'.$delim.'?("[^"]*"|[^'.$delim.']*)/';
		if($count == 1){
			$headers = preg_split('/,/', $row, -1);
			
			$count += 1;
			continue;
		}
		$regExResults = array();
		$vals = preg_match_all($regExp, $row, $regExResults);
		if(count($regExResults[1]) == 0){
			continue;
		}
		$vals = array_slice($regExResults[1],0,count($regExResults[1])-1);
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
		foreach($columnTitles as $key){
			$insertParams[$i] = $arr[$key];
			$i++;
		}
		$result = pg_execute($db, "delete", $deleteParams) or die();
		$result = pg_execute($db, "insert", $insertParams) or die();
		$count += 1;
	}
}

function SQLImportAndReturn($connectString, $schema, $table, 
		$primKeys, $columnTitles, &$theData, $page){
	SQLImport($connectString, $schema, $table, 
		$primKeys, $columnTitles, $theData);
	
	header('location : ' . $page);
}
function SQLImportFromFileAndReturn($connectString, $schema, $table, 
		$primKeys, $columnTitles, $filename, $page){
		
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		
		SQLImportAndReturn($connectString, $schema, $table, 
				$primKeys, $columnTitles, $theData, $page);
}
?>