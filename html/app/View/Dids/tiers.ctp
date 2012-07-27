<option value=""> All </option>
<?php
foreach($tiers as $tier){
	if($selectedTier == $tier){
		echo '<option selected="selected" value="'.$tier.'"> '.$tier.' </option>';
	}
	else{
		echo '<option value="'.$tier.'"> '.$tier.' </option>';
	}
}
?>