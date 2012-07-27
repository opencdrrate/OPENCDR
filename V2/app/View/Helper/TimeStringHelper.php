<?php

class TimeStringHelper extends AppHelper{
	function SecondsToString($seconds){
		$hours = floor($seconds/3600);
		$seconds = $seconds - $hours*3600;
		$minutes = floor($seconds/60);
		$seconds = $seconds - $minutes*60;
		if(strlen($hours) == 1){
			$hours = '0'.$hours;
		}
		if(strlen($minutes) == 1){
			$minutes = '0'.$minutes;
		}
		if(strlen($seconds) == 1){
			$seconds = '0'.$seconds;
		}
		return $hours.':'.$minutes.':'.$seconds;
	}
}
?>