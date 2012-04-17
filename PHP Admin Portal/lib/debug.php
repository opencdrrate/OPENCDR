<?php

$debug = false;
function print_debug($debugString){
	global $debug;
	if($debug){
		echo $debugString. '<br>';
	}
}
?>