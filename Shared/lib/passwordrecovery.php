<?php

include_once 'debug.php';

function MakeNewToken($username, $connectstring){
		$db = pg_connect($connectstring);
		$token = RandomAlphaNum(20);
		print_debug('Inserting token : ' . $token . ' into the passwordrecovery table for user : '. $username);
		$insertStatement = <<< HEREDOC
						INSERT INTO passwordresetmaster(token, username, expires) 
						VALUES ($1, $2, CURRENT_TIMESTAMP + interval '30 minute');;
HEREDOC;
		pg_prepare($db, 'insert' ,$insertStatement);
		pg_execute($db, 'insert', array($token, $username));
		pg_close($db);
		
		return $token;
}

function DoesTokenExist($username, $connectstring){
	print_debug('Checking if customer has token : '. $username);
	$db = pg_connect($connectstring);
	$checkStatement = <<< HEREDOC
	SELECT * FROM passwordresetmaster WHERE username = $1;
HEREDOC;
	pg_prepare($db, 'check', $checkStatement);
	$result = pg_execute($db, 'check', array($username));
	pg_close($db);
	
	if($token = pg_fetch_assoc($result)){
		print_debug('Token exists');
		if(IsPwdTokenExpired($token['expires'], $connectstring)){
			DeleteToken($token['username'], $connectstring);
		}
		return $token['username'];
	}
	else{
		print_debug('Token not found');
		return false;
	}
}

function VerifyToken($token, $connectstring){
	print_debug('Validating token : '. $token);
	$db = pg_connect($connectstring);
	$checkStatement = <<< HEREDOC
	SELECT * FROM passwordresetmaster WHERE token = $1;
HEREDOC;
	pg_prepare($db, 'check', $checkStatement);
	$result = pg_execute($db, 'check', array($token));
	pg_close($db);
	
	if($token = pg_fetch_assoc($result)){
		print_debug('Token is good');
		return $token['username'];
	}
	else{
		print_debug('Token not found');
		return false;
	}
}

function IsPwdTokenExpired($expiryString, $connectstring){
	print_debug('Checking if token is expired');

	$expires = $expiryString;
	
	date_default_timezone_set('America/New_York');
	$unixExpires = strtotime($expires);
	$now = strtotime(date('Y-m-d G:i:s'));
	if($unixExpires < $now){
		print_debug( 'Token expired');
		return true;
	}
	else{
		print_debug( 'Token still valid');
		return false;
	}
}

function DeleteToken($username, $connectstring){
	print_debug('Deleting token, username : '. $username);
	$db = pg_connect($connectstring);
	$deleteStatement = <<< HEREDOC
	DELETE FROM passwordresetmaster WHERE username = $1;
HEREDOC;
	pg_prepare($db, 'delete', $deleteStatement);
	$result = pg_execute($db, 'delete', array($username));
	pg_close($db);
}

function GetCustomerID($username, $connectstring){
	print_debug('Fetching users customerid : ' . $username);
	$db = pg_connect($connectstring);
	$getStatement = 'SELECT customerid FROM webportalaccess WHERE username = $1';
	pg_prepare($db, 'getcustomerid', $getStatement);
	$result = pg_execute($db, 'getcustomerid', array($username));
	pg_close($db);
	if($row = pg_fetch_assoc($result)){
		print_debug('Customer id found : ' . $row['customerid']);
		return $row['customerid'];
	}
	else{
		print_debug('Customerid not found for username : ' . $username);
		return false;
	}
}
?>