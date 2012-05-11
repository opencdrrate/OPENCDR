<?php
	include 'config.php';
	include 'lib/SQLQueryFuncs.php';
	include_once 'DAL/table_callrecordmaster_tbr.php';
	include 'lib/Page.php';
	
	$content = '';
	$table = new psql_callrecordmaster_tbr($connectstring);
	$table->Connect();
	$numberOfRows = $table->CountRatingQueue();
	
	$offset = 0;
	if(isset($_POST["offset"])){
		$offset = $_POST["offset"];
	}
	$query = <<< HEREDOC
		SELECT *
			FROM callrecordmaster_tbr WHERE calltype is not NULL
			order by calldatetime
HEREDOC;
	$limit = 1000;
	$endoffset = min($offset + $limit, $numberOfRows);
	$prevoffset = max($offset - $limit, 0);
	$allArrayResults = $table->SelectRatingQueue($offset, $limit);
	
	$titlespiped = "CallID,CustomerID,CallType,CallDateTime,Duration,Direction,SourceIP,OriginationNumber,DestinationNumber,LRN,CNAMDipped,RateCenter,CarrierID";
	$titles = preg_split("/,/",$titlespiped,-1);
	$htmltable = AssocArrayToTable($allArrayResults,$titles); 
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
			<form action="lib/TBRLibs.php" method="post" id="action">
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
			$limitOptions .= '
			<form action="listresultstbr.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
			<input type="hidden" name="offset" value="'.$prevoffset.'">
			<input type="submit" value="View prev '.$limit.' results"/>
			</form>';
		}
		if($endoffset < $numberOfRows){
		$limitOptions .= '
		<form action="listresultstbr.php" method="post" style=\'margin: 0; padding: 0; display:inline;\'>
		<input type="hidden" name="offset" value="'.$endoffset.'">
		<input type="submit" value="View next '.$limit.' results"/>
		</form>';
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
			var o = $id("progress");
			var progress = o.appendChild(document.createElement("p"));
			progress.appendChild(document.createTextNode("upload " + file.name));

			// progress bar
			xhr.addEventListener("progress", function(e) {
				if (e.lengthComputable) { 
					var pc = (e.loaded / e.total * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output('Progress : ' + Math.round(pc*100)/100 + '%');
				}
				else{
					var pc = e.loaded / total * 100;
					Output('Progress : ' + Math.round(pc*100)/100  + '%');
				}
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					if(xhr.status == 200){
						Output("Done");
					}
					else{
						Output("Error code : " + xhr.status);
					}
					progress.className = (xhr.status == 200 ? "success" : "failure");
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
			var o = $id("progress");
			var progress = o.appendChild(document.createElement("p"));
			progress.appendChild(document.createTextNode("Rating Progress"));
			CategorizeCDR(progress);
		}
		else{
		}
	}
	/*
	"fnCategorizeCDR"()';
	"fnRateIndeterminateJurisdictionCDR"()';
	"fnRateInternationalCDR"()';
	"fnRateInterstateCDR"()';
	"fnRateIntrastateCDR"()';
	"fnRateSimpleTerminationCDR"()';
	"fnRateTieredOriginationCDR"()';
	"fnRateTollFreeOriginationCDR"()';
	*/
	function CategorizeCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (1 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("CategorizeCDR Done<br>");
					RateIndeterminateJurisdictionCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnCategorizeCDR');
		xhr.send();
	}
	function RateIndeterminateJurisdictionCDR(progress){
		Output('Starting RateIndeterminateJurisdictionCDR');
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (2 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateIndeterminateJurisdictionCDR Done<br>");
					RateInternationalCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIndeterminateJurisdictionCDR');
		xhr.send();
	}
	function RateInternationalCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (3 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateIndeterminateJurisdictionCDR Done<br>");
					RateInterstateCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIndeterminateJurisdictionCDR');
		xhr.send();
	}
	function RateInterstateCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (4 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateInterstateCDR Done<br>");
					RateIntrastateCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateInterstateCDR');
		xhr.send();
	}
	function RateIntrastateCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (5 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateIntrastateCDR Done<br>");
					RateSimpleTerminationCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateIntrastateCDR');
		xhr.send();
	}
	function RateSimpleTerminationCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (6 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateSimpleTerminationCDR Done<br>");
					RateTieredOriginationCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateSimpleTerminationCDR');
		xhr.send();
	}
	function RateTieredOriginationCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					var pc = (7 / 8 * 100);
					progress.style.backgroundPosition = parseInt(100 - pc) + "% 0";
					Output("RateTieredOriginationCDR Done<br>");
					RateTollFreeOriginationCDR(progress);
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
		xhr.setRequestHeader("X_SPNAME", 'fnRateTieredOriginationCDR');
		xhr.send();
	}
	function RateTollFreeOriginationCDR(progress){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(e) {
			if (xhr.readyState == 4){
				if(xhr.status == 200){
					progress.style.backgroundPosition = 0 + "% 0";
					Output(xhr.responseText + " All Done<br>");
					progress.className = "success";
				}
				else{
					Output("Error code : " + xhr.status);
					progress.className = "failure";
				}
			}
		};
		xhr.open("POST", "lib/RunSP.php", true);
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