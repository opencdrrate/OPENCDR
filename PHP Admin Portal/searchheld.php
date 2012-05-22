<?php 

$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php'; 
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	
	$today = date("Y-m-d");
	$beginningOfMonth = date("Y-m-d", mktime(0,0,0,date("m"), 1, date("Y")));
		
?>
<head>
<?php echo GetPageHead("Search HELD Calls", "main.php")?>
</head>

<div id="body">

    <form action="listresultsheld.php" method="post" id="searchform">
    <label>Start: </label><input type="text" name="start" value="<?php echo $beginningOfMonth; ?>" /><br />		
    <label>End: </label><input type="text" name="end" value="<?php echo $today;?>"/><br />
    <input type="submit" name="submit" value="Submit" />
    </form>
</div>

<?php echo GetPageFoot("","");?>
</html>