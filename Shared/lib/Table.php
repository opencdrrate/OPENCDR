<?php

class Table{
	private $params;
	private $columnNames;
	public $rows;
	
	function Table($params){
		$this->params = $params;
		$this->columnNames = array();
		$this->rows = array();
	}
	
	function SetColumnNames($columnNames){
		$this->columnNames = $columnNames;
	}
	
	function AddRow($newRow){
		$this->rows[] = $newRow;
	}
	
	function AddColumnName($name){
		$coumnNames[] = $name;
	}
	
	function ToHTML(){
		$htmlTable = <<< HEREDOC
<table {$this->params}>
<thead>
<tr>
HEREDOC;
		foreach($this->columnNames as $col){
			$htmlTable = <<< HEREDOC
				<th>{$col}</th>
HEREDOC;
		}
		$htmlTable .= <<< HEREDOC
</tr>
</thead>
<tbody>
HEREDOC;
		foreach($this->rows as $row){
		$htmlTable .= <<< HEREDOC
<tr>
HEREDOC;
		foreach($row as $cell){
			$htmlTable .= <<< HEREDOC
			<td>{$cell}</td>
HEREDOC;
		}
		$htmlTable .= <<< HEREDOC
<tr>
HEREDOC;
		}
		$tableWidth = count($this->columnNames);
		$htmlTable .= <<< HEREDOC
		</tbody>
	    <tfoot>
	    	<tr>
		    <td colspan="{$tableWidth}"></td>
	    	</tr>
	    </tfoot>
		</table>
HEREDOC;
		
		return $htmlTable;
	}
}
?>