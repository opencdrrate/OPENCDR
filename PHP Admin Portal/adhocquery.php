<html>
<head>

<?php 
	$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
echo GetPageHead("AdHoc Query", "main.php")
?>

</head>
<div id="body">

<form action="exportpipe.php" method="post" id="standardform">
Write your query here: <BR> 
<textarea name="queryString" cols=40 rows=6 /></textarea>
<input type="hidden" name="filename" value="queryresult.csv"/>
<p><input type="submit" />
</form>

</div>
<?php echo GetPageFoot("","");?>
</html> 