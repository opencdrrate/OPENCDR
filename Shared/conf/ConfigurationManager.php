<?php
class ConfigurationManager{
	private $file;
	private $settings;

	function ConfigurationManager(){
		//Default configuration page
		$this->file = $_SERVER["DOCUMENT_ROOT"] . '/Shared/' . 'conf/opencdr.conf'; 
		
		error_reporting(E_ALL);
		date_default_timezone_set('America/New_York');
		ini_set('memory_limit', '200M');
		ini_set('upload_max_filesize', '150MB');
		$this->LoadConfigurationFile($this->file);
	}
	
	function LoadConfigurationFile($file){
		if($file != $this->file){
			$this->file = $file;
		}
		$this->settings = array();
		$handle = fopen($file, 'r');
		/*** loop over the file pointer ***/
		while ( !feof ( $handle) )
		{
			/*** read the line into a buffer ***/
			$buffer = stream_get_line( $handle, 1024, ';' );
			$setting = explode('=', $buffer, 2);
			if(count($setting) < 2){
				continue;
			}
			$this->settings[trim($setting[0])] = trim($setting[1]);
			/*** clear the buffer ***/
			$buffer = '';
		}
		fclose($handle);
	}
	
	function ChangeSetting($name, $newSetting){
		$this->settings[$name] = $newSetting;
	}
	
	function GetSetting($name){
		if($name == 'connectionstring'){
			return $this->BuildConnectionString();
		}
		else{
			if(isset($this->settings[$name])){
				return $this->settings[$name];
			}
			else{
				return '';
			}
		}
	}
	
	function Save(){
		$filename = $this->file;
		// Let's make sure the file exists and is writable first.
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, 'w')) {
				 echo "Cannot open file (".$filename.")";
				 exit;
			}
			foreach($this->settings as $name => $setting){
				$line = $name . " = " . $setting . ';' . PHP_EOL;
				// Write $somecontent to our opened file.
				if (fwrite($handle, $line ) === FALSE) {
					echo "Cannot write to file (".$filename.")";
					exit;
				}
			}
			fclose($handle);
		} 
		else {
			echo "The file ".$filename." is not writable";
		}
	}
	function BuildConnectionString(){ 
		$host = $this->settings['host'];
		$port = $this->settings['port'];
		$dbname = $this->settings['dbname'];
		$user = $this->settings['user'];
		$password = $this->settings['password'];
		$connectstring = <<< HEREDOC
	host={$host} port={$port} dbname={$dbname} user={$user} password={$password}
HEREDOC;
		return $connectstring;
	}
}
?>