<?php
class psql_connection{
	public $error;
	function psql_connection(){
		$this->error = '';
	}
	
	function TestConnectstring($connectstring){
		$connect = false;
		$db = @pg_connect($connectstring);
		if(!$db){
			$this->error = 'No database connection';
			$connect = false;
		}
		else{
			$connect = true;
			pg_close($db);
		}
		return $connect;
	}
}
?>