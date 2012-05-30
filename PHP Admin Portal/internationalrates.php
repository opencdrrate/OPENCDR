<?php
	include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'DAL/table_internationalratemaster.php';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/localizer.php';
	$controllerPath = $sharedFolder . "controllers/ajax_internationalrate_controller.php";
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	$locale = $manager->GetSetting('region');
	$region = new localizer($locale);
	
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
	
	$table = new psql_internationalratemaster($connectstring);
	$table->Connect();
	
	if(isset($_GET['delete'])){
		$deleteList;
		if(isset($_POST['deleteList'])){
			$count = 0;
			$deleteList = $_POST['deleteList'];
			foreach($deleteList as $row){
				$info = explode(',',$row);
				$effectivedate = $info[0];
				$billedprefix = $info[1];
				$deleteArray = array('effectivedate'=>$effectivedate, 'billedprefix'=>$billedprefix, 'customerid'=>$customerid);
				
				if($table->Delete($deleteArray)){
					$count++;
				}
			}
			$content .= '<font color="red">'.$count . ' rows deleted.</font	><br>';
		}
		else{
		}
	}
	if(isset($_GET['deleteall'])){
		pg_query("delete from internationalratemaster where customerid='".$customerid."'");
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
	<!-- Sample rate sheets -->
	<form action="https://sourceforge.net/projects/opencdrrate/files/Sample%20Rate%20Sheets/" method="post" target="_blank">
	<input type="submit" class="btn blue add-customer" value="Sample rate sheets">
	</form>
	
	<!-- THE EXPORT BUTTON -->
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="{$fullQuery}" name="queryString">
		<input type="hidden" value="{$customerid}.csv" name="filename">
	</form>
	
	<!-- THE IMPORT BUTTON -->
	<form id="action" action="{$controllerPath}?customerid={$customerid}" method="POST">
	Choose a file to import: <input name="uploadedFile" type="File" id="fileselect" />
	</form>
	<button id="uploadbutton" type="submit">Import File </button><br>
	Showing rows : {$offset} to {$endoffset} <br>
	Total number of rows : {$numberOfRows}
	<br>
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
	
	$htmltable = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th><button type="button" onclick="confirmDelete()" id="deleteButton">Delete Selected Rates</button></th>
<th>effectivedate</th>
<th>billedprefix</th>
<th>retailrate</th>
</tr>
</thead>
<tbody>
HEREDOC;
$i=0;
foreach($assocArray as $row){
	$htmltable .= <<< HEREDOC
	<tr>
	<td><Input type="checkbox" name="deleteList[{$i}]" value="{$row['effectivedate']},{$row['billedprefix']}"/></td>
	<td>{$region->FormatDate($row['effectivedate'])}</td>
	<td>{$row['billedprefix']}</td>
	<td>{$region->FormatCurrency($row['retailrate'])}</td>
	</tr>
HEREDOC;
	$i++;
}
	$htmltable .= <<< HEREDOC
	</tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="4"></td>
	    	</tr>
	    </tfoot>
		</table>
HEREDOC;
	$table->Disconnect();
	$javascripts = <<< HEREDOC
	<script type="text/javascript">
function confirmDelete(){
	var agree=confirm("Are you sure you want to delete these rates?");
	if (agree){
		document.forms["deleteAction"].submit();
		return true;
	}
	else{
	}
}

function confirmDeleteAll(){
var agree=confirm("Are you sure you want to delete ALL rates?");
	if (agree){
		document.forms["deleteAllAction"].submit();
		return true;
	}
	else{
	}
}
</script>
HEREDOC;
?>


	<?php echo GetPageHead('View Customer International Rates', 'rates.php', $javascripts);?>
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
	<br>
	<button name="deleteAllButton" onclick="confirmDeleteAll()">Delete All</button>
	<br>
	<form action="internationalrates.php?deleteall=1&customerid=<?php echo $customerid?>" method="POST" name="deleteAllAction">
	</form>
	<form action="internationalrates.php?delete=1&customerid=<?php echo $customerid?>" method="POST" name="deleteAction">
	<?php echo $htmltable;?>
	</form>
	
	</div>
	<script src="<?php echo $sharedFolder;?>lib/jUpload.js"></script>
	<?php echo GetPageFoot();?>
