<option value=""> All </option>
<?php
foreach($latas as $lata){
	if($selectedLata == $lata){
		echo '<option selected="selected" value="'.$lata.'"> '.$lata.' </option>';
	}
	else{
		echo '<option value="'.$lata.'"> '.$lata.' </option>';
	}
}
?>