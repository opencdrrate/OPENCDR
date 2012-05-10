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
function confirmRateCalls(){
	var agree=confirm("This will rate 1 day worth of CDR for each call type. This may take several minutes to run.");
	if (agree){
		window.location = "ratecalls.php";

	}
	else{
	}
}
</script>
HEREDOC;
 ?>
 
		<?php echo GetPageHead("List Results TBR", "main.php", $javaScripts);?>
		
		<div id="body">
			
			<br>
			<form action="javascript:confirmRateCalls()" method="post">
				<input type="submit" class="btn blue add-customer" value="Rate Calls">
			</form>
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

	// file selection
	function FileSelectHandler(e) {
		var fileselect = $id("fileselect");
		var file = fileselect.files[0];

		ParseFile(file);
	}
	
	function UploadHandler(e) {
		var fileselect = $id("fileselect");
		var file = fileselect.files[0];
		//alert(file.name);
		//UploadFileByLine(file);
		UploadFile(file);
	}

	// output file information
	function ParseFile(file) {
		// display text
		//if (file.type.indexOf("text") == 0) {

				var reader = new FileReader();
				reader.onload = function(e) {
				Output(
					"<p>File information: <strong>" + file.name +
					"</strong> type: <strong>" + file.type +
					"</strong> size: <strong>" + file.size +
					"</strong> bytes</p>" +
					"<pre>" +
					e.target.result.replace(/</g, "&lt;").replace(/>/g, "&gt;") +
					"</pre>"
				);
			}
			reader.readAsText(file);
		//}
	}
	
	function UploadFileByLine(file){
		var myFileSysObj = new ActiveXObject("Scripting.FileSystemObject");
		var myInputTextStream = myFileSysObj.OpenTextFile(file.name, 1, true);
		
		while(!myInputTextStream.AtEndOfStream){
			var line = myInputTextStream.ReadLine();
			alert(line);
		}
		
		myInputTextStream.Close();
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

	// initialize
	function Init() {

		var fileselect = $id("fileselect"),
			uploadbutton = $id("uploadbutton");

		// file select
		//fileselect.addEventListener("change", FileSelectHandler, false);
		uploadbutton.addEventListener("click", UploadHandler, false);
	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}
})();
</script>
	<?php echo GetPageFoot();?>