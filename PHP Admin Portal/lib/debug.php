<?php

$debug = true;
function print_debug($debugString){
	global $debug;
	if($debug){
		echo $debugString. '<br>';
	}
}
?>