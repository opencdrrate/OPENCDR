<?php

abstract class SQLTable{
	public $rowsAdded = 0;
	public $rowsDeleted = 0;
	abstract function Insert($row);
	abstract function Delete($row);
	abstract function DoesExist($row);
	abstract function SelectAll();
	
	abstract function Connect();
	abstract function Disconnect();
	
	function Upsert($old, $new){
		if($this->DoesExist($old)){
			if($this->Delete($old)){
				return $this->Insert($new);
			}
		}
		else{
			return $this->Insert($new);
		}
	}
	
	function Update($old, $new){
		if($this->DoesExist($old)){
			if($this->Delete($old)){
				return $this->Insert($new);
			}
			else
			{
				return false;
			}
		}
		else{
			#doesn't exist
			return false;
		}
	}
}

?>