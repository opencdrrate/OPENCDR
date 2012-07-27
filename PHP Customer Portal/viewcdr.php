<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/session.php';
include_once $path . 'DAL/table_webportalaccesstokens.php';
include_once $path . 'DAL/table_webportalaccess.php';
include_once $path . 'DAL/table_callrecordmaster.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/calendar/classes/tc_calendar.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

if(!isset($_GET['token'])){
	#You need to be logged in to view this page
	header('location: login.php?error=notloggedin');
}
$token = $_GET['token'];

if(IsTokenExpired($token, $connectstring)){
	header('location: login.php?error=notloggedin');
}
else{
	UpdateExpiry($token,$connectstring);
}

$content = <<< HEREDOC
	<a href="login.php?token={$token}&logout=1">logout</a>
HEREDOC;
$table = '';
$webportalaccess = new psql_webportalaccesstokens($connectstring);
$webportalaccess->Connect();
$webportalaccessInfo = $webportalaccess->Select($token);
$webportalaccess->Disconnect();
$username = $webportalaccessInfo[0]['customerid'];

$user = new psql_webportalaccess($connectstring);
$user->Connect();
$userInfo = $user->Select($username);
$user->Disconnect();
$customerid = $userInfo[0]['customerid'];

?>
<script type='text/javascript' src='/Shared/lib/Table.js'></script>
<script language="javascript" src="/Shared/lib/calendar/calendar.js"></script>
<script type="text/javascript">
function $name(name){
        return document.getElementsByName(name)[0].value;
    }
function $id(id) {
	return document.getElementById(id);
}
function Output(msg) {
		var m = $id("table");
		m.innerHTML = msg;
	}
function LoadDateRange(Start, End){
	var startdate = $name("startdate");
	var enddate = $name("enddate") + " 23:59:59";

	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(e) {
		if (xhr.readyState == 4) {
			if(xhr.status == 200){
				var xmlDoc=xhr.responseXML;
				//Output(xmlDoc);
				var table = XMLToTable(xmlDoc);
				Output(table.ToHtml(0,0));
			}
			else{
				Output("Error code : " + xhr.status);
			}
		}
		else{
			Output("<blink>Please wait...</blink>");
		}
	};
	xhr.open("POST", 'controllers/viewcdr_controller.php?FUNCTION=SELECTDATE&TOKEN=<?php echo $token?>', true);
	xhr.setRequestHeader("STARTDATE", startdate);
	xhr.setRequestHeader("ENDDATE", enddate);
	xhr.send();
}
function LoadTable(){
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(e) {
		if (xhr.readyState == 4) {
			if(xhr.status == 200){
				var xmlDoc=xhr.responseXML;
		
				var table = XMLToTable(xmlDoc);
				Output(table.ToHtml(0,0));
				
			}
			else{
				Output("Error code : " + xhr.status);
			}
		}
		else{
			Output("<blink>Please wait...</blink>");
		}
	};
	xhr.open("POST", 'controllers/viewcdr_controller.php?FUNCTION=SELECT&TOKEN=<?php echo $token?>', true);
	xhr.send();
}
function DownloadData(){
	window.location = 'controllers/viewcdr_controller.php?FUNCTION=DOWNLOAD&TOKEN=<?php echo $token?>';
}
LoadTable();
</script>
<?php echo GetPageHead('View CDRs', 'main.php?token='.$token, '');?>
<div id='body'>
<form name="SelectDates" action="javascript:LoadDateRange()" method="post">
	<label>Start date</label><br>
	  <?php
		
	  	$image = "/Shared/calendar/images/iconCalendar.gif";
		$calendarPath = "/Shared/lib/calendar/";
		
		$myCalendar = new tc_calendar("startdate", true, false);
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath($calendarPath);
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?><br>
	<label>End date</label><br>
		  <?php
		
	  	$image = "/Shared/lib/calendar/images/iconCalendar.gif";
		$calendarPath = "/Shared/lib/calendar/";
		
		$myCalendar = new tc_calendar("enddate", true, false);
	  	$myCalendar->setIcon($image);
	  	$myCalendar->setDate(date('d'), date('m'), date('Y'));
	  	$myCalendar->setPath($calendarPath);
	  	$myCalendar->setYearInterval(2010, 2020);
	  	$myCalendar->dateAllow('2010-01-01', '2020-12-31');
	  	$myCalendar->setDateFormat('j F Y');
	  	$myCalendar->setAlignment('left', 'bottom');
	  	$myCalendar->setSpecificDate(array("2010-12-25"), 0, 'year');
	 	$myCalendar->writeScript();
	?><br>
	<button type="submit">Search CDRs</button>
</form>
<form name="export" action="javascript:DownloadData()" method="post">
   	<input type="submit" class="btn orange export" value="Export table to CSV">
</form><br>
<?php echo $content;?><br>
<div id="table"></div>
</div>
<?php echo GetPageFoot();?>