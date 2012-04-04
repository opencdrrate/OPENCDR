<?php
$npa = $_GET["npa"];
?>
<html><body>
<form action="npamaster.php" method="POST">
	Enter new state: <input name="newState" type="text"/>
	<input name="npa" value="<?php echo $npa; ?>" type="hidden"/>
	<input type="hidden" name="edit" value="1"/>
	<input type="submit" value="Submit"/>
</form>
</body></html>