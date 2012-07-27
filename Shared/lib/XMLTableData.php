<?php
class XMLTableData{
	private $Rows = array();
	private $Columns = array();
	
	function XMLTableData(){
	}
	function AddColumn($column){
		$this->Columns[] = $column;
	}
	function AddRow($row){
		$this->Rows[] = $row;
	}
	function ToXML(){
		$xml = '<data>';
		foreach($this->Columns as $column){
			$xml .= <<< HEREDOC
			<title>{$column}</title>
HEREDOC;
		}
		
		foreach($this->Rows as $row){
			$xml .= '<row>';
			foreach($row as $cell){
			$xml .= <<< HEREDOC
				<cell>
				{$cell}
				</cell>
HEREDOC;
			}
			$xml .= '</row>';
		}
		$xml .= '</data>';
		return $xml;
	}
	function ToCSV(){
		$csv = '';
		$csv .= implode(',', $this->Columns);
		$csv .= "\r\n";
		
		foreach($this->Rows as $row){
			$csv .= implode(',', $row);
			$csv .= "\r\n";
		}
		return $csv;
	}
}
?>