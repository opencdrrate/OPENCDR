<?php
	include 'lib/Page.php';
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';
	
	$content = '';
	$db = pg_connect($connectstring);
	set_time_limit(0);
	
	$table = 'internationalratemaster';
	$customerNumberField = 'customerid';
	$customerid = $_GET["customerid"];
	
	if(isset($_POST["import"])){
	
					$deleteStatement = <<< HEREDOC
						DELETE FROM {$table} 
						WHERE "customerid" = $1 
							AND "effectivedate" = $2 
							AND "billedprefix" = $3;
HEREDOC;
					$insertStatement = <<< HEREDOC
						INSERT INTO {$table}("customerid","effectivedate","billedprefix","retailrate")
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
				list($effectivedate,$billedprefix,$retailrate) = $data;
				if($j == 1){
					$j++;
					continue;
				}
				if(substr($billedprefix, 0,1) != '+'){
					$billedprefix = '+' . $billedprefix;
				}
					$deleteParams = array($customerid, $effectivedate,$billedprefix);
					$insertParams = array($customerid, $effectivedate,$billedprefix, $retailrate);

					$result = pg_execute($db, "delete", $deleteParams);
					$result = pg_execute($db, "insert", $insertParams);
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
	$limit = 10000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);


	$fullQuery = "SELECT effectivedate,billedprefix,retailrate FROM " . $table 
		. " WHERE " . $customerNumberField . " = '" . $customerid . "'"
		. " ORDER BY effectivedate,billedprefix";
	$limitedQuery = $fullQuery
		. " LIMIT "
		. $limit
		. " OFFSET "
		. $offset	
		. ";";

	$content = <<< HEREDOC
	A valid file is a csv with the following as the first line: <br><br>
	effectivedate,billedprefix,retailrate<p><br>
	Every subsequent line will have the appropriate data.<br>
	Note that there are <b>no surrounding quotes anywhere</b> and <b>all column names are case sensitive</b>.<br>
HEREDOC;
	$content .= <<<HEREDOC
		
	<!-- THE EXPORT BUTTON -->
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="{$fullQuery}" name="queryString">
		<input type="hidden" value="{$customerid}.csv" name="filename">
	</form>
	
	<!-- THE IMPORT BUTTON -->
	<form enctype="multipart/form-data" action="internationalrates.php?customerid={$customerid}" method="POST">
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
		<form action="internationalrates.php?customerid='.$customerid.'&offset='.$prevoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="hidden" name="query" value="1"/>
		<input type="submit" value="View prev '.$limit.' results"/>
		</form>';
	}
	if($endoffset < $numberOfRows){
	$content .= '
	<form action="internationalrates.php?customerid='.$customerid.'&offset='.$endoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
	<input type="hidden" name="query" value="1"/>
	<input type="submit" value="View next '.$limit.' results"/>
	</form>';
	}
	
	$limitedQueryResult = pg_query($db, $limitedQuery);
	$assocArray = array();
	while($row = pg_fetch_assoc($limitedQueryResult)){
		$assocArray[] = $row;
	}
	$content .= AssocArrayToTable($assocArray, array('effectivedate','billedprefix','retailrate'));
	pg_close($db);
?>


	<?php echo GetPageHead("View Customer Rates");?>
	<div id="body">
	<?php echo $content;?>
	</div>
	<?php echo GetPageFoot();?>
