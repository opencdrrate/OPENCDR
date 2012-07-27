<?php

class AsteriskCDR extends AppModel{
	var $name 'AsteriskCDR';
    public $useDbConfig = 'asteriskdb';
	var $useTable = 'cdr';
	var $primaryKey = 'uniqueid';
	
	/*SELECT * from ".$this->table_name." where amaflags <> 100 and uniqueid > 0 order by calldate LIMIT 1000*/
	function ResetAMAFlags(){
		$this->query('UPDATE ' . $this->useTable . ' SET amaflags = 0 WHERE amaflags = 100');
	}
}
?>