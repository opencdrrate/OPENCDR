<?php
	include 'lib/Page.php';
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';
	include 'lib/FileUtils/InterstateRateFileImporter.php';
	include 'DAL/table_interstateratemaster.php';
	
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
	
	$table = new psql_interstateratemaster($connectstring);
	$table->Connect();
	
	$numberOfRows = $table->CountRows($customerid);
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	
	$limit = 5000;
	$assocArray = $table->LimitedQuery($customerid, $limit, $offset);
	
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	$fullQuery = "SELECT effectivedate,npanxxx,retailrate FROM ".$table->table_name
			. " WHERE customerid = '" . $customerid . "'"
			. " ORDER BY effectivedate,npanxxx";
	$content .= <<<HEREDOC
		
	<!-- THE EXPORT BUTTON -->
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="{$fullQuery}" name="queryString">
		<input type="hidden" value="{$customerid}.csv" name="filename">
	</form>
	
	<!-- THE IMPORT BUTTON -->
	<form id="action" action="controllers/ajax_interstaterate_controller.php?customerid={$customerid}" method="POST">
	Choose a file to import: <input name="uploadedFile" type="File" id="fileselect" />
	</form>
	<button id="uploadbutton" type="submit">Import File </button><br>
	Showing rows : {$offset} to {$endoffset} <br>
	Total number of rows : {$numberOfRows}
	<br>
HEREDOC;
		
	if($offset > 0){
		$content .= '
		<form action="interstaterates.php?customerid='.$customerid.'&offset='.$prevoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="customerid" value="'.$customerid.'">
		<input type="hidden" name="query" value="1"/>
		<input type="submit" value="View prev '.$limit.' results"/>
		</form>';
	}
	if($endoffset < $numberOfRows){
	$content .= '
	<form action="interstaterates.php?customerid='.$customerid.'&offset='.$endoffset.'" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
	<input type="hidden" name="query" value="1"/>
	<input type="submit" value="View next '.$limit.' results"/>
	</form>';
	}
	
	$content .= AssocArrayToTable($assocArray, array('effectivedate','npanxxx','retailrate'));
	
	$table->Disconnect();
?>


	<?php echo GetPageHead('View Customer Interstate Rates', 'rates.php');?>
	<div id="body">
	<?php echo $errors;?>
	<?php
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$memory_limit = (int)(ini_get('memory_limit'));
	echo 'Max file size ' . $max_upload . 'mb <br>';
	echo 'Memory limit ' . $memory_limit . 'mb <br>';
	?>
	<br>
	<div id="progress"></div>
	<div id="messages"></div>

	<?php echo $content;?>
	</div>
	<script src="lib/jUpload.js"></script>
	<?php echo GetPageFoot();?>
