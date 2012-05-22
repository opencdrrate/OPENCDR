<?php
include_once 'SQLTable.php';

/*
CREATE TABLE `cdr` (
  `calldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clid` varchar(80) NOT NULL DEFAULT '',
  `src` varchar(80) NOT NULL DEFAULT '',
  `dst` varchar(80) NOT NULL DEFAULT '',
  `dcontext` varchar(80) NOT NULL DEFAULT '',
  `channel` varchar(80) NOT NULL DEFAULT '',
  `dstchannel` varchar(80) NOT NULL DEFAULT '',
  `lastapp` varchar(80) NOT NULL DEFAULT '',
  `lastdata` varchar(80) NOT NULL DEFAULT '',
  `duration` int(11) NOT NULL DEFAULT '0',
  `billsec` int(11) NOT NULL DEFAULT '0',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `amaflags` int(11) NOT NULL DEFAULT '0',
  `accountcode` varchar(20) NOT NULL DEFAULT '',
  `uniqueid` varchar(32) NOT NULL DEFAULT '',
  `userfield` varchar(255) NOT NULL DEFAULT '',
  `recordingfile` varchar(255) NOT NULL DEFAULT '',
  `did` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/

/*
SELECT * from cdr where amaflags = 100 and uniqueid > 0 order by calldate LIMIT 1000;
*/
class mysql_cdr extends SQLTable{
	public $table_name = 'cdr';
	private $host;
	private $user;
	private $password;
	private $database;
	private $port;
	
	private $connection;
	function mysql_cdr($host, $user, $password, $database, $port = 3306){
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
		$this->port = $port;
	}
	function Disconnect(){
	}
	function Connect(){
		$this->connection = new mysqli($this->host,$this->user,$this->password,$this->database, $this->port);
		if ($this->connection->connect_errno) {
			$msg = "Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error;
			throw new Exception($msg);
		}
		return $this->connection->host_info;
	}
	
	function Insert($row){
		throw new Exception('This function is not implemented yet');
	}
	function Delete($row){
		throw new Exception('This function is not implemented yet');
	}
	function DoesExist($row){
		throw new Exception('This function is not implemented yet');
	}
	//For now updates ama flag only
	function Update($old, $new){
		$newAmaflag = $new['amaflags'];
		$oldCallid = $old['uniqueid'];
		$updateString = <<< HEREDOC
		UPDATE {$this->table_name} SET amaflags={$newAmaflag}
			WHERE uniqueid={$oldCallid};
HEREDOC;
		$res = $this->connection->query($updateString);
		
		$verifyString = <<< HEREDOC
		SELECT * from {$this->table_name}
		WHERE uniqueid = {$oldCallid} and amaflags = {$newAmaflag};
HEREDOC;
		$verifyResult = $this->connection->query($verifyString);
		if ($verifyResult->num_rows > 0){
			return true;
		}
		else{
			return false;
		}
		if (!$res) {
			throw new Exception("Could not successfully run query (".$sqlQuery .") from DB: " . mysql_error());
		}
		return true;
	}
	function SelectAll(){
		throw new Exception('This function is not implemented yet');
	}
	function SelectTop1000(){
		$sqlQuery = "SELECT * from ".$this->table_name." where amaflags <> 100 and uniqueid > 0 order by calldate LIMIT 1000";
		$res = $this->connection->query($sqlQuery);
		$assoc_array = array();
		
		if (!$res) {
			throw new Exception("Could not successfully run query (".$sqlQuery .") from DB: " . mysql_error());
		}
		if ($res->num_rows == 0) {
			return $assoc_array;
		}
		while($row = $res->fetch_assoc()){
			$assoc_array[] = $row;
		}
		
		return $assoc_array;
	}
	
	function ResetAmaflags(){
        $sqlStatement = <<< HEREDOC
            UPDATE {$this->table_name} SET amaflags = 0 WHERE amaflags = 100
HEREDOC;
        $res = $this->connection->query($sqlStatement);
        if (!$res){
                throw new Exception("Could not succesfully run query " . $sqlStatement . " : " . mysql_error());
        }
    }

	
	function GetTitles(){
		throw new Exception('This function is not implemented yet');
	}
	function GetRowView($row){
		throw new Exception('This function is not implemented yet');
	}
}
?>