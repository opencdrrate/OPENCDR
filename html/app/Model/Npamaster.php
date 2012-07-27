<?php
class Npamaster extends AppModel {
	var $name = 'Npamaster';
	var $useTable = 'npamaster';
	var $primaryKey = 'rowid';
	var $actsAs = array('ImportCsv');
	
	function loadtype($line, $type = null){
		$data = str_getcsv($line);
		$item['npa'] = $data[0];
		$item['state'] = $data[1];
		
		return $item;
	}
}
