
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
	
	function EnableProgress(value, max){
		var p = $id("progress");
		p.innerHTML = '<progress value="'+value+'" max="'+max+'"/>';
	}
	function HideProgress(){
		var p = $id("progress");
		p.innerHTML = '';
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
			EnableProgress(0,100);

			// progress bar
			xhr.addEventListener("progress", function(e) {
				var pc = 0;
				if (e.lengthComputable) { 
					pc = (e.loaded / e.total * 100);
				}
				else{
					pc = e.loaded / total * 100;
				}
				Output('Progress : ' + Math.round(pc*100)/100  + '%');
				EnableProgress(pc,100);
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					if(xhr.status == 200){
						Output("Done : <A HREF=\"javascript:location.reload(true)\">Refresh</a><br>");
					}
					else{
						Output("Error code : " + xhr.status);
					}
					HideProgress();
				}
				else{
					Output("<blink>Please wait...</blink>");
				}
			};

			// start upload
			xhr.open("POST", $id("action").action, true);
			xhr.setRequestHeader("X_FILESIZE", file.size);
			xhr.setRequestHeader("X_FILENAME", file.name);
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