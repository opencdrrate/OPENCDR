<?php
	include 'lib/Page.php';
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';
	$errors = '';
	function customError($errno, $errstr)
	{
		global $errors;
		$errors .= '<font color="red">'.$errstr.'</font><br>';
	}
	set_error_handler("customError");
	$content = '';
	$db = pg_connect($connectstring);
	set_time_limit(0);
	
	$table = 'intrastateratemaster';
	$customerNumberField = 'customerid';
	$customerid = $_GET["customerid"];
	if(isset($_POST["import"])){
	
					$deleteStatement = <<< HEREDOC
						DELETE FROM {$table} 
						WHERE "customerid" = $1 
							AND "effectivedate" = $2 
							AND "npanxxx" = $3;
HEREDOC;
					$insertStatement = <<< HEREDOC
						INSERT INTO {$table}("customerid","effectivedate","npanxxx","retailrate")
						VALUES ($1,$2,$3,$4);
HEREDOC;
	
		$result = pg_prepare($db, "delete", $deleteStatement);
		$result = pg_prepare($db, "insert", $insertStatement);
					
		$myFile = $_FILES['uploadedFile']['tmp_name'];
		if($myFile == ""){
			$content .= "Invalid File";
		}
		else{
			$handle = fopen($myFile, 'r');
			$j = 1;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				foreach($data as $word){
					str_replace('"',"",$word);
				}
				list($effectivedate,$npanxxx,$retailrate) = $data;
				$deleteParams = array($customerid, $effectivedate,$npanxxx);
				$insertParams = array($customerid, $effectivedate,$npanxxx, $retailrate);

				$result = pg_execute($db, "delete", $deleteParams);
				if(!$result){
					trigger_error('Error on line ' .$j . ' of file.');
				}
				$result = pg_execute($db, "insert", $insertParams);
				if(!$result){
					trigger_error('Error on line ' .$j . ' of file.');
				}
				$j++;
			}
			fclose($handle);
		}
	}
	
    $queryNumberofRows = 'SELECT count(*) FROM '.$table.' WHERE "customerid" = \''.$customerid.'\';';
	$numOfRowsResult = pg_query($queryNumberofRows) or die(print pg_last_error());
	$numberOfRowsArray = pg_fetch_row($numOfRowsResult);
	$numberOfRows = $numberOfRowsArray[0];
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	$limit = 5000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);


	$fullQuery = "SELECT effectivedate,npanxxx,retailrate FROM " . $table 
		. " WHERE " . $customerNumberField . " = '" . $customerid . "'"
		. " ORDER BY effectivedate,npanxxx";
	$limitedQuery = $fullQuery
		. " LIMIT "
		. $limit
		. " OFFSET "
		. $offset	
		. ";";

	$content .= <<<HEREDOC
		
	<!-- THE EXPORT BUTTON -->
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="{$fullQuery}" name="queryString">
		<input type="hidden" value="{$customerid}.csv" name="filename">
	</form>
	
	<!-- THE IMPORT BUTTON -->
	<form enctype="multipart/form-data" action="intrastaterates.php?customerid={$customerid}" method="POST">
	<input type="hidden" name="import" value="1"/>
	Choose a file to import: <input name="uploadedFile" type="File" />
	<input type="submit" value="import File" />
	</form>

	Showing rows : {$offset} to {$endoffset} <br>
	Total number of rows : {$numberOfRows}
	<br>'
HEREDOC;
		
	if($offset > 0){
		$content .= '
		<form action="intrastaterates.php?customerid='.$customerid.'&offset='.$prevoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="hidden" name="query" value="1"/>
		<input type="submit" value="View prev '.$limit.' results"/>
		</form>';
	}
	if($endoffset < $numberOfRows){
	$content .= '
	<form action="intrastaterates.php?customerid='.$customerid.'&offset='.$endoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
	<input type="hidden" name="query" value="1"/>
	<input type="submit" value="View next '.$limit.' results"/>
	</form>';
	}
	
	$limitedQueryResult = pg_query($db, $limitedQuery);
	$assocArray = array();
	while($row = pg_fetch_assoc($limitedQueryResult)){
		$assocArray[] = $row;
	}
	$content .= AssocArrayToTable($assocArray, array('effectivedate','npanxxx','retailrate'));
	pg_close($db);
?>


	<?php echo GetPageHead('View Customer Intrastate Rates', 'rates.php');?>
	<div id="body">
	<?php echo $errors;?>
	<?php echo $content;?>
	</div>
	<?php echo GetPageFoot();?>
