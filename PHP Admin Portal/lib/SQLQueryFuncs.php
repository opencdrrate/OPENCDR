<?php

function SQLCountRows($connectString, $schema, $table, $predicate = ""){
	$db = pg_connect($connectString);
	$query = "SELECT COUNT (*) FROM \""
		.$schema . "\".\""
		.$table 
		. "\" " . $predicate
		. ";";
	$result = pg_query($query);
	if (!$result) {
		echo pg_last_error();
		exit();
	}
	$row = pg_fetch_assoc($result);
	foreach ($row as $key => $value){
		$count = intval($value);
		break;
	}
	
	return $count;
}
function SQLSelectQuery($connectString, $selectQuery, $delimiter, $newlinechar, $quoted = false){
	$db = pg_connect($connectString);
	$query = $selectQuery;
	$output = "";
	$result = pg_query($query);
	$quotes = '';
	if($quoted){
		$quotes = '"';
	}
	if (!$result) {
		echo pg_last_error();
		exit();
	}

	$count = 1;
	while($myrow = pg_fetch_assoc($result)) {
		if($count == 1){
			$first = 1;
			foreach ($myrow as $key => $value) {
				if($first != 1){
					$output .= $delimiter;
				}
				$output .= $quotes."$key".$quotes;
				$first = $first + 1;
			}
		}
		$first = 1;
			$output .= $newlinechar;
		foreach ($myrow as $key => $value) {
			if($first != 1){
				$output .= $delimiter;
			}
			$output .= $quotes."$value".$quotes;
			$first = $first + 1;
		}
		$count = $count + 1;
    }
	return $output;
}
function SaveQueryResultsToCSV($connectString, $queryResult, $filepath){
	$file = fopen($filepath, 'w') or fopen("files/default.csv", 'w'); 
	fwrite($file, "$queryResult");
	fclose($file);
}
function QueryResultToTable($queryResult, $delimiter, $titles = array()){
	$allRows = preg_split('/\n/', $queryResult, -1);
	$output = "";
	$output .= '<Table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">';
	$output .= "<tr><thead>";
	$first = true;
	if(count($titles) == 1){
		$titles = $allRows[0];
	}
	foreach($titles as $title){
		$output .= "<th>";
		$output .= $title;
		$output .= "</th>";
	}
	$output .= "</thead></tr><tbody>";
	
	foreach($allRows as $row){
		if($first){
			$first = false;
			continue;
		}
		$allEntries = preg_split('/,/',$row,-1);
		$output .= "<tr>";
		foreach($allEntries as $entry){
			$output .= "<td>";
			$output .= $entry;
			$output .= "</td>";
		}
		$output .= "</tr>";
	}
	$numberOfCols = count($titles);
	$output .= <<<HEREDOC
	</tbody>
	<tfoot>
	    	<tr>
		    <td colspan="{$numberOfCols}"></td>
	    	</tr>
	    </tfoot></Table><br/><br/>
HEREDOC;
	return $output;
}

function CreateDropDown($connectString, $name, $table){
$query = 'select '.$name.' from '.$table.' order by '.$name.';';
$db = pg_connect($connectString);
$result = pg_query($db, $query);
if (!$result) {
	echo pg_last_error();
	exit();
}

$dropdown = '<select name="'.$name.'">';
$dropdown .= '<option></option>';

while($myrow = pg_fetch_assoc($result)) { 
	$dropdown .= '<option value="'.$myrow[$name].'">'.$myrow[$name].'</option>';
}

$dropdown .= '</select>';

return $dropdown;
}

?>