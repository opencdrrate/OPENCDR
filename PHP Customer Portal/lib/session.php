<?php
$debug = true;
function print_debug($debugString){
	global $debug;
	if($debug){
		echo $debugString.'<br>';
	}
}

function BeginSession($username, $connectstring){
	print_debug( 'Attempting to begin session');
	if(UserIsOnline($username, $connectstring)){
		$token = GetToken($username, $connectstring);
		print_debug( 'User logged in with token : ' . $token);
		return $token;
	}
	else{
		$db = pg_connect($connectstring);
		$token = GenerateToken($username);
		
		#insert token and username into table
		$insertStatement = <<< HEREDOC
	INSERT INTO webportalaccesstokens(token, customerid, expires)
    VALUES ($1, $2, CURRENT_TIMESTAMP + interval '30 minute');
HEREDOC;
		$result = pg_query_params($insertStatement, array($token, $username));
		
		pg_close($db);
		
		print_debug( 'User logged in with token : ' . $token);
		return $token;
	}
}

function UserIsOnline($username, $connectstring){
	print_debug('Checking if '.$username.' is online');
	$db = pg_connect($connectstring);
	$queryStatement = "SELECT expires FROM webportalaccesstokens WHERE customerid = $1";
	$result = pg_query_params($db,$queryStatement, array($username));
	$resultArr = pg_fetch_assoc($result);
	pg_close($db);
	if($resultArr){
		print_debug($username .' found');
		return true;
	}
		print_debug($username .' not found');
	return false;
}

function TokenExists($token, $connectstring){
	print_debug( 'Checking if token exists');
	$db = pg_connect($connectstring);
	$queryStatement = "SELECT expires FROM webportalaccesstokens WHERE token = $1";
	$result = pg_query_params($db,$queryStatement, array($token));
	$resultArr = pg_fetch_assoc($result);
	pg_close($db);
	if($resultArr){
		print_debug( 'Token found');
		
		UpdateExpiry($token, $connectstring);
		return true;
	}
	print_debug( 'Token not found');
	return false;
}

function GetToken($username, $connectstring){
	print_debug('Fetching token for '. $username);
	$db = pg_connect($connectstring);
	$selectStatement = "SELECT token FROM webportalaccesstokens WHERE customerid = $1;";
	$result = pg_query_params($selectStatement, array($username));
	$resultArray = pg_fetch_assoc($result);
	pg_close($db);
	if(!$result){
		print_debug('Token not found');
		return false;
	}
		print_debug('Token found : ' . $resultArray['token']);
		return $resultArray['token'];
}

function GenerateToken($username){
	include_once './../PHP Admin Portal/Lib/encryption.php';
	return substr(hash_password($username, RandomAlphaNum(5)),0,10);
}

function EndSession($token, $connectstring){
	print_debug( 'Ending session');
	$db = pg_connect($connectstring);
	$deleteString = <<< HEREDOC
	DELETE FROM webportalaccesstokens WHERE token = $1;
HEREDOC;
	$result = pg_query_params($db, $deleteString, array($token));
	pg_close($db);
}

function UpdateExpiry($token, $connectstring){
	print_debug( 'Updating expiry token' );
	$db = pg_connect($connectstring);
	$updateString = <<< HEREDOC
	UPDATE webportalaccesstokens SET expires = CURRENT_TIMESTAMP + interval '30 minute' WHERE token = $1;
HEREDOC;
	$result = pg_query_params($db, $updateString, array( $token));
	pg_close($db);
}

function IsTokenExpired($token, $connectstring){
	print_debug( 'Checking if token is expired');

	$db = pg_connect($connectstring);
	$queryStatement = "SELECT expires FROM webportalaccesstokens WHERE token = $1";
	$result = pg_query_params($db,$queryStatement, array($token));
	pg_close($db);
	$resultArr = pg_fetch_assoc($result);
	if(!$resultArr){
		print_debug( 'Token not found');
		return true;
	}
	$expires = $resultArr['expires'];
	
	date_default_timezone_set('America/New_York');
	$unixExpires = strtotime($expires);
	$now = strtotime(date('Y-m-d G:i:s'));
	if($unixExpires < $now){
		print_debug( 'Token expired');
		print_debug( 'Deleting token');
		
		$db = pg_connect($connectstring);
		$deleteStatement = "DELETE FROM webportalaccesstokens WHERE token = $1";
		$result = pg_query_params($db,$deleteStatement, array($token));
		pg_close($db);
		
		return true;
	}
	else{
		print_debug( 'Token still valid');
		return false;
	}
}
?>