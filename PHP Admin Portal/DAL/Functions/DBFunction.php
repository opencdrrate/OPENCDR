<?php
class DBFunction{
	protected $FunctionName;
	protected $connectstring;
	function DBFunction($name, $connectString){
		$this->FunctionName = $name;
		$this->connectstring = $connectString;
	}
	
	function Run($params = array()){
		$runString = <<< HEREDOC
		select "{$this->FunctionName}"()
HEREDOC;
		$db = pg_connect($this->connectstring);
		$queryResult = pg_query($db, $runString);
		$resultRow = pg_fetch_row($queryResult);
		pg_close($db);
		return $resultRow;
	}
}
?>