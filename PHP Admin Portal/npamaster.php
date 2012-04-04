<?php
include 'lib/Page.php';
include 'lib/SQLQueryFuncs.php';
include 'config.php';
include 'lib/SQLImport.php';

function edit($connectstring, $npa, $newStateValue){
	$updateString = "UPDATE \"public\".\"npamaster\" SET "
		. "\"state\" = $1"
		. " WHERE "
		. "npa = $2;";

	$db = pg_connect($connectstring);
	pg_prepare($db, "update", $updateString);
	pg_execute($db, "update", array($newStateValue, $npa));
}

function delete($connectstring, $npa){
	$deleteString = "DELETE FROM \"public\".\"npamaster\" WHERE "
		. " \"npa\" = $1;";

	$db = pg_connect($connectstring);
	pg_prepare($db, "delete", $deleteString);
	pg_execute($db, "delete", array($npa));
}

function import($connectstring, $myFile){
	if($myFile == ""){
		echo "Invalid File";
		echo '<!--';
	}
	else{
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		
		$primKeys = array("npa");
		$columnTitles = array("npa","state");
		SQLImport($connectstring, "public", "npamaster", 
			$primKeys, $columnTitles, $theData);
			
	}
}
if(isset($_GET["refresh"])){
	header('location : npamaster.php');
}
else if(isset($_POST["edit"])){
	$npa = $_POST["npa"];
	$newStateValue = $_POST["newState"];
	edit($connectstring, $npa, $newStateValue);
}
else if(isset($_GET["delete"])){
	$npa = $_GET["npa"];
	delete($connectstring,$npa);
}
else if(isset($_POST["import"])){
	$myFile = $_FILES['uploadedFile']['tmp_name'];
	import($connectstring, $myFile);
}

#begin query
$fullQuery = "SELECT npa, state FROM \"npamaster\""
 . " ORDER BY \"npa\";";
$db = pg_connect($connectstring);
$queryResult = pg_query($db,$fullQuery);
?>
<?php
$script = <<< HEREDOC
<script type="text/javascript">
function confirmSubmit(npa){
	var agree=confirm("Are you sure you want to delete this row?");
	if (agree){
		return true ;
	}
	else
		return false ;
}
</script>
HEREDOC;
?>
 
<?php


$table = <<< HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th>NPA</th>
<th>State</th>
<th>Edit</th>
<th>delete</th>
</tr>
</thead><tbody>
HEREDOC;

while($row = pg_fetch_assoc($queryResult)){
	$npa = $row['npa'];
	$table .= "<tr>";
	$table .= "<td>".$row['npa']."</td>";
	$table .= "<td>".$row['state']."</td>";
		$table .= "<td>";
		$table .= " <form action=\"editnpaform.php\" method=\"GET\">";
		$table .= "  <input type=\"hidden\" name=\"npa\" value=\"".$npa."\" \>";
		$table .= "  <input type=\"hidden\" name=\"edit\" value=\"1\"\>";
		$table .= "  <input type=\"submit\" class=\"btn-action update\" value=\"edit\"\>";
		$table .= " </form>";
		$table .= "</td>";
		
		$table .= "<td>";
		$table .= " <form action=\"npamaster.php\" method=\"GET\">";
		$table .= "  <input type=\"hidden\" name=\"npa\" value=\"".$npa."\"\>";
		$table .= "  <input type=\"hidden\" name=\"delete\" value=\"1\"\>";
		$table .= "  <input type=\"submit\" class=\"btn-action delete\" value=\"delete\"
					onClick=\"return confirmSubmit(".$npa.")\"\>";
		$table .= " </form>";
		$table .= "</td>";
	$table .= "</tr>";
}
$table .= <<<HEREDOC
			</tbody><tfoot><tr>
		    <td colspan="4"></td>
	    	</tr></tfoot></table><br/<br/>
HEREDOC;
pg_close($db);
?>
 <?php echo GetPageHead("NPA Master", "main.php", $script);?>
<div id="body">
<h2>How to make a valid file to import</h2>
A valid file must be a comma seperated file (.CSV) with the names of the headers on the first line<br>
ie. the first line should be "npa,state" without the quotation marks.<br>
<p>
Every new line should describe a row with the npa first followed by the state seperated by commas<br>
ie. "613,ON" without the quotes.<br>
NOTE: Do not leave any white space in the header
<p>
You can find additional NPAs in excel format at the 
<a href="http://www.nationalnanpa.com/nas/public/npasInServiceByNumberReport.do?method=displayNpasInServiceByNumberReport">
NANPA website</a>
<form enctype="multipart/form-data"  action="npamaster.php" method="POST">
	<input name="uploadedFile" type="File"/>
	<input type="hidden" name="import" value="1"/>
	<input type="submit" value="Import"/>
</form>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>