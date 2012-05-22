<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/SQLQueryFuncs.php';
	include_once $path . 'DAL/table_callrecordmaster_tbr.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'conf/ConfigurationManager.php';
	include_once $path . 'lib/localizer.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();
	$locale = $manager->GetSetting('region');
	$region = new localizer($locale);
	
	$content = '';
	$table = new psql_callrecordmaster_tbr($connectstring);
	$table->Connect();
	$numberOfRows = $table->CountRatingQueue();
	$query = <<< HEREDOC
		SELECT *
			FROM callrecordmaster_tbr WHERE calltype is not NULL
			order by calldatetime
HEREDOC;
	
	$offset = 0;
	if(isset($_GET["offset"])){
		$offset = $_GET["offset"];
	}
	$limit = 1000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	$allArrayResults = $table->SelectRatingQueue($offset, $limit);
	
	$titlespiped = "CallID,CustomerID,CallType,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDipped,RateCenter,CarrierID";
	$titles = preg_split("/,/",$titlespiped,-1);
	$htmltable = AssocArrayToTable($allArrayResults,$titles); 
	/*"CallID,CustomerID,CallType,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,
	LRN,CNAMDipped,RateCenter,CarrierID";*/
	$htmltable = <<< HEREDOC
	<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
	<thead><tr>
	<th>CallID</th>
	<th>CustomerID</th>
	<th>CallType</th>
	<th>CallDateTime</th>
	<th>Duration</th>
	<th>Direction</th>
	<th>SourceIP</th>
	<th>OriginationNumber</th>
	<th>DestinationNumber</th>
	<th>LRN</th>
	<th>CNAMDipped</th>
	<th>RateCenter</th>
	<th>CarrierID</th>
	</tr></thead><tbody>
HEREDOC;
	foreach($allArrayResults as $row){
		$htmltable .= <<< HEREDOC
		<tr>
		<td>{$row['callid']}</td>
		<td>{$row['customerid']}</td>
		<td>{$row['calltype']}</td>
		<td>{$region->FormatDateTime($row['calldatetime'])}</td>
		<td>{$row['duration']}</td>
		<td>{$row['direction']}</td>
		<td>{$row['sourceip']}</td>
		<td>{$row['originatingnumber']}</td>
		<td>{$row['destinationnumber']}</td>
		<td>{$row['lrn']}</td>
		<td>{$row['cnamdipped']}</td>
		<td>{$row['ratecenter']}</td>
		<td>{$row['carrierid']}</td>
		</tr>
HEREDOC;
	}
	$htmltable .= <<< HEREDOC
	</tbody><tfoot><tr>
	<td colspan="15"></td>
	</tr></tfoot></table>
HEREDOC;
	$table->Disconnect();
?> 

<?php

$javaScripts = <<< HEREDOC
<script type="text/javascript">

</script>
HEREDOC;
 ?>
 
		<?php echo GetPageHead("List Results TBR", "main.php", $javaScripts);?>
		
		<div id="body">
			
			<br>
				<input type="submit" class="btn blue add-customer" value="Rate Calls" id="ratebutton"/>
			<form name="export" action="exportpipe.php" method="post">
				<input type="submit" class="btn orange export" value="Export Table">
				<input type="hidden" name="queryString" value="<?php echo htmlspecialchars($query);?>">
				<input type="hidden" name="filename" value="TBRExport.csv">
			</form>
			<br>
			<form action="/Shared/lib/TBRLibs.php" method="post" id="action">
				<select name="type" id="type">
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
					<option value="asterisk">Asterisk</option>
					<option value="itel">iTel</option>
				</select>
				<input name="uploadedFile" type="File" id="fileselect" />
			</form>
			<button id="uploadbutton" type="submit">Import File </button>
			<br>
			<div id="progress"></div>
			<div id="messages"></div>
			<?php
			$limitOptions = <<< HEREDOC
		Showing rows : {$offset} to {$endoffset} <br>
		Total number of rows : {$numberOfRows}
		<br>
HEREDOC;

		if($offset > 0){
			$limitOptions .= <<< HEREDOC
			<a href="listresultstbr.php?offset={$prevoffset}"><<< View prev {$limit} results</a>
HEREDOC;
		}
		if($endoffset < $numberOfRows){
			$limitOptions .= <<< HEREDOC
			| <a href="listresultstbr.php?offset={$endoffset}">View next {$limit} results >>></a>
HEREDOC;
		}
		echo $limitOptions;
			?>
			<?php echo $htmltable; ?>
	
		</div>
	
<script>

/*
"messages"
"fileselect"
"progress"
"action"
"uploadbutton"
*/
(function() {

	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}

	// output information
	function Output(msg) {
		var m = $id("messages");
		m.innerHTML = msg;
	}
	
	function EnableProgress(value, max){
		var p = $id("progress");
		p.innerHTML = '<progress value="'+value+'" max="'+max+'"/>';
	}
	function HideProgress(){
		var p = $id("progress");
		p.innerHTML = '';
	}
	
	function UploadHandler(e) {
		var fileselect = $id("fileselect");
		var file = fileselect.files[0];
		//alert(file.name);
		//UploadFileByLine(file);
		UploadFile(file);
	}

	// upload JPEG files
	function UploadFile(file) {
		var total = file.size;
		var xhr = new XMLHttpRequest();
			// create progress bar
			EnableProgress(0,100);

			// progress bar
			xhr.addEventListener("progress", function(e) {
				var pc = 100;
				if (e.lengthComputable) { 
					pc = (e.loaded / e.total * 100);
				}
				else{
					pc = e.loaded / total * 100;
				}
					Output('Progress : ' + Math.round(pc*100)/100 + '%');
				EnableProgress(pc,100);
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					if(xhr.status == 200){
						HideProgress();
						Output("Done : <A HREF=\"javascript:location.reload(true)\">Refresh</a><br>");
					}
					else{
						Output("Error code : " + xhr.status);
					}
				}
				else{
					Output("<blink>Please wait...</blink>");
				}
			};

			// start upload
			xhr.open("POST", $id("action").action, true);
			xhr.setRequestHeader("X_FILESIZE", file.size);
			xhr.setRequestHeader("X_FILENAME", file.name);
			xhr.setRequestHeader("TYPE", $id("type").value);
			xhr.send(file);
	}
	function confirmRateCalls(arg){
		var agree=confirm("This will rate 1 day worth of CDR for each call type. This may take several minutes to run.");
		if (agree){
			// create progress bar
			EnableProgress(0,8);
			CategorizeCDR();
		}
		else{
		}
	}
	function CategorizeCDR(){
		Output('<blink>Categorizing CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(1,8);
						Output("CategorizeCDR Done<br>");
						RateIndeterminateJurisdictionCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnCategorizeCDR');
		xhr.send();
	}
	function RateIndeterminateJurisdictionCDR(){
		Output('<blink>Rating Indeterminate CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(2,8);
						Output("RateIndeterminateJurisdictionCDR Done<br>");
						RateInternationalCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIndeterminateJurisdictionCDR');
		xhr.send();
	}
	function RateInternationalCDR(){
		Output('<blink>Rating International CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(3,8);
						Output("RateIndeterminateJurisdictionCDR Done<br>");
						RateInterstateCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIndeterminateJurisdictionCDR');
		xhr.send();
	}
	function RateInterstateCDR(){
		Output('<blink>Rating Interstate CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(4,8);
						Output("RateInterstateCDR Done<br>");
						RateIntrastateCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateInterstateCDR');
		xhr.send();
	}
	function RateIntrastateCDR(){
		Output('<blink>Rating Intrastate CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(5,8);
						Output("RateIntrastateCDR Done<br>");
						RateSimpleTerminationCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIntrastateCDR');
		xhr.send();
	}
	function RateSimpleTerminationCDR(){
		Output('<blink>Rating Simple Termination CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						EnableProgress(6,8);
						Output("RateSimpleTerminationCDR Done<br>");
						RateTieredOriginationCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateSimpleTerminationCDR');
		xhr.send();
	}
	function RateTieredOriginationCDR(){
		Output('<blink>Rating Tiered Origination CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					EnableProgress(7,8);
					if(!xhr.responseText){
						Output("RateTieredOriginationCDR Done<br>");
						RateTollFreeOriginationCDR();
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateTieredOriginationCDR');
		xhr.send();
	}
	function RateTollFreeOriginationCDR(){
		Output('<blink>Rating Toll Free Origination CDR. <br>Please wait...</blink>');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					if(!xhr.responseText){
						HideProgress();
						Output("Done : <A HREF=\"javascript:location.reload(true)\">Refresh</a><br>");
					}
					else{
						Output(xhr.responseText);
						HideProgress();
					}
				}
				else{
					Output("Error code : " + xhr.status);
					HideProgress();
				}
			}
		};
		xhr.open("POST", "/Shared/lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateTollFreeOriginationCDR');
		xhr.send();
	}
	
	// initialize
	function Init() {

		var fileselect = $id("fileselect"),
			uploadbutton = $id("uploadbutton"),
			ratebutton = $id("ratebutton");

		// file select
		//fileselect.addEventListener("change", FileSelectHandler, false);
		ratebutton.addEventListener("click",confirmRateCalls, false);
		uploadbutton.addEventListener("click", UploadHandler, false);
	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}
})();
</script>
	<?php echo GetPageFoot();?>