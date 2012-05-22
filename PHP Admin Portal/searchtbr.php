<?php

$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
	include_once $path . 'lib/Page.php'; 
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	
	$today = date("Y-m-d");
	$beginningOfMonth = date("Y-m-d", mktime(0,0,0,date("m"), 1, date("Y")));
	?>
<head>
<meta charset="UTF-8">

<!-- Stylesheet -->

<link rel="stylesheet" type="text/css" media="screen" href="stylesheets/style.css" />

<style type="text/css">

#searchform label {float: left;padding: 7px 0 0 0;width: 35px;
 }
     
#searchform input {
border: 1px solid #ccc;
 border-radius: 4px;
 padding: 5px;
 }
  
#searchform input.submit {
margin: 5px 0 0 35px;
 }        
    
</style>
<?php echo GetPageHead("Search TBR Calls", "main.php")?>
</head>

<div id="body">
	
    <form action="listresultstbr.php" method="post" id="searchform">		
    <label>Start: </label><input type="text" name="start" value="<?php echo $beginningOfMonth;?>"/><br />
    <label>End:   </label><input type="text" name="end" value="<?php echo $today;?>"/><br />
    <input type="submit" name="submit" value="Submit" />
    </form>
 
</div>

<?php echo GetPageFoot("","");?>
</html>