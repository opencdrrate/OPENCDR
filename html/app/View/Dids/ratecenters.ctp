<option value=""> All </option>
<?php
foreach($ratecenters as $ratecenter){
	if($ratecenter == $selectedRatecenter){
		echo '<option selected="selected" value="'.$ratecenter.'"> '.$ratecenter.' </option>';
	}
	else{
		echo '<option value="'.$ratecenter.'"> '.$ratecenter.' </option>';
	}
}
?>