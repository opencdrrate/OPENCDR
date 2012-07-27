<?php

require_once(HTML2PS_DIR.'fetcher._interface.class.php');
class FetcherCake extends Fetcher{
	var $html;
	
	function get_data($data_id){
		return new FetchedDataURL($data_id,
                                array(),
                                '');
	}
	
	function get_base_url() {
		return '';
	}

	function error_message() {
		die("Oops. Inoverridden 'error_message' method called in ".get_class($this));
	}
  
	/** PRIVATE FUNCTIONS BELOW HERE **/
	function _processCakeRequest(){
	}
}

?>