<?php

include 'lib/SQLQueryFuncs.php';
include 'config.php';

$query = $_POST["queryString"];
$filename = $_POST["filename"];
$delim = ",";
$quoted = false;
if(isset($_POST["delimiter"])){
	$delim = $_POST["delimiter"];
}
if(isset($_POST['quoted'])){
	$quoted = true;
}
$filepath = "files/".$filename;
$queryResult = SQLSelectQuery($connectstring, $query, $delim, "\r\n", $quoted);
SaveQueryResultsToCSV($connectstring, $queryResult, $filepath);
?>
<html><body>
Done<br>
<a href="main.php">Back to main</a>
<script type="text/javascript">
    window.location = "<?php echo $filepath?>"
</script>
</body>
</html>