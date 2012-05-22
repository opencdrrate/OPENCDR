<?php
/*listrate centers.php

allows the user to view, delete, update, add entries to the tieredoriginationratecentermaster table*/
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

$results = '';
$db = pg_connect($connectstring);

if(isset($_POST['function'])){
	$function = $_POST['function'];
	if($function == 'add'){
		$insertStatement = "INSERT INTO tieredoriginationratecentermaster(ratecenter, tier) "
				."VALUES ('".$_POST['ratecenter']."','".$_POST['tier']."')";
		pg_query($insertStatement);
		$results .= "Row added<br>";
	}
	else if($function == 'delete'){
		$row = $_GET['rowid'];
		$deleteStatement = "DELETE FROM tieredoriginationratecentermaster "
							."WHERE rowid = '".$row."';";
		pg_query($deleteStatement);
		$results .= "Row deleted<br>";
	}
	else if($function == 'update'){
		$row = $_GET['rowid'];
		$tier = $_POST['tier'];
		$updateStatement = "UPDATE tieredoriginationratecentermaster "
							."SET tier='".$tier."' "
							."WHERE rowid='".$row."';";
		pg_query($updateStatement);
		$results .= "Row updated<br>";
	}
}

$query = "SELECT * from tieredoriginationratecentermaster;";
$queryResult = pg_query($query);
$items = pg_fetch_all($queryResult);
if($items == null){
	$items = array();
}

$table = <<<HEREDOC
<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
<thead>
HEREDOC;
$table .= 	<<<HEREDOC
			<thead>
			<tr>
			<th>Rate Center</th>
			<th>Tier</th>
			<th></th>
			<th></th>
			<th></th>
			</tr>
			</thead>
			<tbody>
HEREDOC;

$table .= <<< HEREDOC
			<tr>
			<form action="listratecenters.php" method= "POST">
				<input type="hidden" name="function" value="add"/>
				<td><input type="text" name="ratecenter" value="<New rate center>" size=30></td>
				<td><input type="text" name="tier" value="<New tier>" size=10></td>
				<td><input type="submit" value="Add"/></td>
				<td></td>
			</form>
			</tr>
HEREDOC;
foreach($items as $item){
	$table .= '<tr>';
	$table .= '<td>'.$item['ratecenter'].'</td>';
	$table .= 	'<form action="listratecenters.php?rowid='.$item['rowid'].'" method="POST">'
					.'<input type="hidden" name="function" value="update"/>'
					.'<td><input type="text" name="tier" size=3 value='.$item['tier'].'></td>'
					.'<td><input type="submit" class="btn-action update" value="Update"/></td>'
				.'</form>';
	$table .= 	'<form action="listratecenters.php?rowid='.$item['rowid'].'" method="POST">'
					.'<input type="hidden" name="function" value="delete"/>'
					.'<td><input type="submit" class="btn-action delete" value="Delete"/></td>'
				.'</form>';
	$table .= '</tr>';
}
$table .= '</tbody></table>';

?>

<?php echo GetPageHead("List Rate Centers");?>
<div id="body">
<?php echo $results;?>
<?php echo $table;?>
</div>
<?php echo GetPageFoot();?>