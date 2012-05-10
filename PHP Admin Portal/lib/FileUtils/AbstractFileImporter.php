<?php
abstract class AbstractFileImporter{
	protected $handle;
	protected $sqlTable;
	protected $lineCount = 0;
	public $delim = ',';
	
	function AbstractFileImporter($table){
		$this->sqlTable = $table;
	}
	function Open($filename){
		$this->handle = fopen($filename, 'r');
		
		if($this->handle == false){
			#error
			$error = 'Error opening file : ' . $filename;
			throw new Exception($error);
		}
	}
	function Close(){
		fclose($this->handle);
	}
	
	function ImportAll($delim = ',', $suppress = true){
		$this->sqlTable->Connect();
		$this->delim = $delim;
		while (($data = fgetcsv($this->handle, 0, $this->delim)) !== FALSE) {
			$params = $this->ParseLine($data);
			if(!$params){
				continue;
			}
			try{
				$this->sqlTable->Upsert($params['oldParams'], $params['newParams']);
				if(!$suppress){
					echo implode(',',$data);
				}
			}
			catch(Exception $e){
				trigger_error($e->getMessage() . ' on line ' . $this->lineCount);
			}
			$this->lineCount++;
		}
		$this->sqlTable->Disconnect();
		
		return $this->GetSummary();
	}
	
	function ImportLine(){
		if(($data = fgetcsv($this->handle, 0, $this->delim)) !== FALSE){
			$params = $this->ParseLine($data);
			if(!$params){
				return false;
			}
			try{
				$this->sqlTable->Upsert($params['oldParams'], $params['newParams']);
			}
			catch(Exception $e){
				trigger_error($e->getMessage() . ' on line ' . $this->lineCount);
			}
			$this->lineCount++;
			return implode(',',$data);
		}
		else{
			return false;
		}
	}
	
	function GetSummary(){
		$content = '';
		$itemsInserted = $this->sqlTable->rowsAdded - $this->sqlTable->rowsDeleted;
		$itemsReplaced = $this->sqlTable->rowsDeleted;
		$content .= <<< HEREDOC
			{$itemsInserted} rates inserted<br>
			{$itemsReplaced} rates updated<br>
HEREDOC;
		return $content;
	}
	/*
		return array('oldParams', 'newParams');
	*/
	abstract function ParseLine($data);
}
?>