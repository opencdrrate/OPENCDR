<?php
	include 'lib/Page.php';
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';
	include 'DAL/table_simpleterminationratemaster.php';
	
	$errors = '';
	function customError($errno, $errstr)
	{
		global $errors;
		if($errors == ''){
			$errors .= '<font color="red">'.$errstr.'</font><br>';
		}
	}
	set_error_handler("customError");
	
	$content = '';
	$customerid = $_GET["customerid"];
	
	$table = new psql_simpleterminationratemaster($connectstring);
	$table->Connect();
	if(isset($_POST["import"])){
	
		$myFile = $_FILES['uploadedFile']['tmp_name'];
		if($myFile == ""){
			trigger_error("Please choose a file");
		}
		else{
			$handle = fopen($myFile, 'r');
			$j = 1;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				foreach($data as $word){
					str_replace('"',"",$word);
				}
				list($effectivedate,$billedprefix,$retailrate) = $data;
				$oldParams = array('customerid'=>$customerid,
									'effectivedate'=>$effectivedate,
									'billedprefix'=>$billedprefix);
				$newParams = array('customerid'=>$customerid, 
									'effectivedate'=>$effectivedate,
									'billedprefix'=>$billedprefix, 
									'retailrate'=>$retailrate);
				try{
					$table->Update($oldParams, $newParams);
				}
				catch(Exception $e){
					trigger_error($e->getMessage() . ' on line ' . $j);
				}
				$j++;
			}
			fclose($handle);
			$itemsInserted = $table->rowsAdded - $table->rowsDeleted;
			$itemsReplaced = $table->rowsDeleted;
			$content .= <<< HEREDOC
			{$itemsInserted} rates inserted<br>
			{$itemsReplaced} rates updated<br>
HEREDOC;
		}
	}
	
	$numberOfRows = $table->CountRows($customerid);
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	
	$limit = 5000;
	$assocArray = $table->LimitedQuery($customerid, $limit, $offset);
	
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	$fullQuery = "SELECT effectivedate,billedprefix,retailrate FROM ".$table->table_name
			. " WHERE customerid = '" . $customerid . "'"
			. " ORDER BY effectivedate,billedprefix";
	$content .= <<<HEREDOC
		
	<!-- THE EXPORT BUTTON -->
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="{$fullQuery}" name="queryString">
		<input type="hidden" value="{$customerid}.csv" name="filename">
	</form>
	
	<!-- THE IMPORT BUTTON -->
	<form enctype="multipart/form-data" action="simpletermination.php?customerid={$customerid}" method="POST">
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
		<form action="simpletermination.php?customerid='.$customerid.'&offset='.$prevoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="hidden" name="query" value="1"/>
		<input type="submit" value="View prev '.$limit.' results"/>
		</form>';
	}
	if($endoffset < $numberOfRows){
	$content .= '
	<form action="simpletermination.php?customerid='.$customerid.'&offset='.$endoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
	<input type="hidden" name="query" value="1"/>
	<input type="submit" value="View next '.$limit.' results"/>
	</form>';
	}
	
	$content .= AssocArrayToTable($assocArray, array('effectivedate','billedprefix','retailrate'));
	
	$table->Disconnect();
?>


	<?php echo GetPageHead('View Customer Simple Termination Rates', 'rates.php');?>
	<div id="body">
	<?php echo $errors;?>
	<?php
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$memory_limit = (int)(ini_get('memory_limit'));
	echo 'Max file size ' . $max_upload . 'mb <br>';
	echo 'Memory limit ' . $memory_limit . 'mb <br>';
	?>
	<?php echo $content;?>
	</div>
	<?php echo GetPageFoot();?>
