<?php

abstract class SQLTable{
	abstract function Insert($row);
	abstract function Delete($row);
	abstract function DoesExist($row);
	abstract function Update($old, $new);
	abstract function SelectAll();
	
	abstract function Connect();
	abstract function Disconnect();
}

?>