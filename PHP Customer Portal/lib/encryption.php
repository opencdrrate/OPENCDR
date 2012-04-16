<?php
include_once 'debug.php';
$site_key = 'k1t3t8';
function hash_password($password, $nonce) {
  global $site_key;
  return hash_hmac('sha512', $password . $nonce, $site_key);
}

function ValidateUsernamePassword($username, $password, $connectionString){
	print_debug('Validating '.$username.' and his password');
	$db = pg_connect($connectionString) or die('Couldn\'t connect to database');
	
	$select_query = "SELECT HashedPassword, Nonce FROM webportalaccess WHERE username = $1";
	$result = pg_query_params($db, $select_query, array($username));
	$db_data = pg_fetch_assoc($result);
	pg_close($db);
	
	if($db_data == false){
		#user not found
		print_debug('User not found');
		return false;
	}
	
	$db_hash = $db_data['hashedpassword'];
	$db_nonce = $db_data['nonce'];
	
	return hash_password($password, $db_nonce) == $db_hash;
}

function RegisterNewUser($username, $customerid, $password,$connectionString){
	print_debug('Entering new user into the database : '. $username);
	$db = pg_connect($connectionString) or die('Couldn\'t connect to database');
	$select_query = "SELECT Username FROM webportalaccess WHERE Username = $1";
	$result = pg_query_params($db, $select_query, array($username)) or die('Query failed');
	$db_data = pg_fetch_assoc($result);
	
	if(!$db_data){
		#generate a random nonce (10 digits)
		$random_nonce = RandomAlphaNum(10);
		
		$hash = hash_password($password, $random_nonce);
		$insert_command = "INSERT INTO webportalaccess(Username, Nonce, CustomerID, HashedPassword)
							VALUES ($1,$2,$3,$4)";
		pg_query_params($db,$insert_command,array($username,$random_nonce,$customerid,$hash));
		pg_close($db);
		return true;
	}
	else{
		print_debug('Error : a user by that name already exists.');
		pg_close($db);
		return false;
	}
}

function ResetPassword($username, $connectionString){
	print_debug('Resetting password for : '. $username);
	$db = pg_connect($connectionString) or die('Couldn\'t connect to database');
		
	$nonce = RandomAlphaNum(10);
	$hash = hash_password('', $nonce);
	$update_command = "UPDATE webportalaccess SET HashedPassword = $1, Nonce = $2 
						WHERE Username = $3;";
	pg_query_params($db, $update_command, array($hash, $nonce, $username)) or die('Query failed');
	
	pg_close($db);
}

function ChangePassword($username, $old_password, $new_password, $connectionString){
	print_debug('Changing password for : '. $username);
	
	if(ValidateUsernamePassword($username, $old_password, $connectionString)){
		$db = pg_connect($connectionString) or die('Couldn\'t connect to database');
		
		$nonce = RandomAlphaNum(10);
		$hash = hash_password($new_password, $nonce);
		$update_command = "UPDATE webportalaccess SET HashedPassword = $1, Nonce = $2 
							WHERE Username = $3;";
		pg_query_params($db, $update_command, array($hash, $nonce, $username)) or die('Query failed');
		
		pg_close($db);
		return true;
	}
	else{
		print_debug('Validation failed');
		return false;
	}
}

function RandomAlphaNum($length){
	$result = '';
	for($i = 0; $i < $length; $i++){
		$rangeMin = 1; 
		$rangeMax = 35; 
		$base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number
		$newRand = base_convert($base10Rand, 10, 36); //convert it
		$result .= (string)$newRand;
	}
    return $result; //spit it out

} 
?>