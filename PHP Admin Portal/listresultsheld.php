<?php

include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'DAL/table_callrecordmaster_held.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/localizer.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$locale = $manager->GetSetting('region');
$region = new localizer($locale);

$helpPage = <<< HEREDOC
	https://sourceforge.net/p/opencdrrate/home/Rating%20Errors/
HEREDOC;

if(isset($_GET['move'])){
	$db = pg_connect($connectstring);
	
	$moveString = 'SELECT "fnMoveHELDCDRToTBR"();';
	pg_query($db, $moveString);
	
	pg_close($db);
}

	$query = 'SELECT * FROM callrecordmaster_held;';
	$table = new psql_callrecordmaster_held($connectstring);
	$table->Connect();
	$numberOfRows = $table->CountResults();
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	$limit = 1000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	
	$table->Disconnect();

		$limitOptions = <<< HEREDOC
			Showing rows : {$offset} to {$endoffset} <br>
			Total number of rows : {$numberOfRows}
			<br>
HEREDOC;
		if($offset > 0){
		$limitOptions .= <<< HEREDOC
		<a href="listresultsheld.php?offset={$prevoffset}"><<< View prev {$limit} results    </a>
HEREDOC;
		}
		if($endoffset < $numberOfRows){
		$limitOptions .= <<< HEREDOC
		<a href="listresultsheld.php?offset={$endoffset}">View next {$limit} results >>></a>
HEREDOC;
		}
?>

<?php echo GetPageHead("Rating Errors","main.php");?>
<script type='text/javascript' src='/Shared/lib/Table.js'></script>
<div id="body">
	<form name="export" action="exportpipe.php" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
		<input type="hidden" value="<?php echo htmlspecialchars($query);?>" name="queryString">
	<input type="hidden" value="HELDExport" name="filename">
	</form>
	<form action="listresultsheld.php?move=1" method="post">
   	<input type="submit" class="btn blue add-customer" value="Move to Rating Queue"/>
	</form>
	<form action="<?php echo $helpPage;?>" method="post" target="_blank">
   	<input type="submit" class="btn orange export" value="HELP"/>
	</form>
	<?php echo $limitOptions;?><br>
<div id="table"></div>
</div>
<script type="text/javascript">
	function $id(id) {
		return document.getElementById(id);
	}
	function Output(msg) {
		var m = $id("table");
		m.innerHTML = msg;
	}
	
	function LoadTable(offset,limit){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4) {
				if(xhr.status == 200){
					var xmlDoc=xhr.responseXML;
					var table = XMLToTable(xmlDoc);
					Output(table.ToHtml(offset,limit));
					//Output('<pre>'+ xmlDoc + '</pre>');
				}
				else{
					Output("Error code : " + xhr.status);
				}
			}
			else{
				Output("<blink>Please wait...</blink>");
			}
		};
		xhr.open("POST", 'controllers/listresultsheld_controller.php', true);
		xhr.send();
	}
	
	function TableSort(a){
	var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4) {
				if(xhr.status == 200){
					var xmlDoc=xhr.responseXML;
					var table = XMLToTable(xmlDoc);
					table.QuickSortAll(a, true);
					
					Output(table.ToHtml(<?php echo $offset?>,<?php echo $limit?>));
				}
				else{
					Output("Error code : " + xhr.status);
				}
			}
			else{
				Output("<blink>Please wait...</blink>");
			}
		};
		xhr.open("POST", 'controllers/listresultsheld_controller.php', true);
		xhr.send();
	}
	
LoadTable(<?php echo $offset?>,<?php echo $limit?>);
</script>
	<?php echo GetPageFoot();?>