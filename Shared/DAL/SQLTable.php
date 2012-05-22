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
	
	abstract function GetTitles();
	abstract function GetRowView($row);
	
	function ToHTMLTable(){
		$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
		include_once $path . 'lib/Table.php';
		$data = $this->SelectAll();
		$htmlTable = new Table('id="listcostumer-table" border="0" cellspacing="0" cellpadding="0"');
		$htmlTable->SetColumnNames($this->GetTitles());
		foreach($data as $row){
			$htmlTable->AddRow($this->GetRowView($row));
		}
		return $htmlTable;
	}
?>