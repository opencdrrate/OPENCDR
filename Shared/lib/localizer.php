<?php
if (!class_exists('DateTime')) {
	class DateTime {
		public $date;

		public function __construct($date) {
			$this->date = strtotime($date);
		}

		public function setTimeZone($timezone) {
			return;
		}

		private function __getDate() {
			return date(DATE_ATOM, $this->date);
		}

		public function modify($multiplier) {
			$this->date = strtotime($this->__getDate() . ' ' . $multiplier);
		}

		public function format($format) {
			return date($format, $this->date);
		}
	}
} 

class localizer{
	public $region;
	function localizer($region = 'CAD'){
		$this->region = $region;
	}
	
	function FormatCurrency($number){
		$region = $this->region;
		
		if($this->region == 'CAD'){
			return '$'.number_format($number, 2, '.',',');
		}
		else if($this->region == 'USD'){
			return '$'.number_format($number, 2, '.',',');
		}
		else if($this->region == 'EUR'){
			return '&euro;'.number_format($number, 2, ',',',');
		}
	}
	
	function FormatDate($stringdate){
		if(empty($stringdate)){
			return '';
		}
		
		$date = new DateTime($stringdate);
		if($this->region == 'CAD'){
			return $date->format('d-m-Y');
		}
		else if($this->region == 'USD'){
			return $date->format('m-d-Y');
		}
		else if($this->region == 'EUR'){
			return $date->format('d-m-Y');
		}
	}
	
	function FormatDateTime($stringdate){
		if(empty($stringdate)){
			return '';
		}
		$date = new DateTime($stringdate);
		
		return $this->FormatDate($stringdate) . ' ' . $date->format('H:i:s');
	}
}
?>