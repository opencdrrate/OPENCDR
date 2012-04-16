<?php
include_once 'debug.php';

function EmailExists($emailaddress, $connectstring){
	print_debug('Checking if email exists');
	$db = pg_connect($connectstring);
	#check if email exists
	$checkStatement = 'SELECT customerid FROM customercontactmaster WHERE primaryemailaddress = $1;';
	$result = pg_prepare($db, 'check', $checkStatement);
	$result = pg_execute($db, 'check', array($emailaddress));
	pg_close($db);
	if($row = pg_fetch_assoc($result)){
		print_debug('email: ' . $emailaddress . ' found, username = '. $row['customerid']);
		return $row['customerid'];
	}
	else{
		print_debug('email: ' . $emailaddress . ' not found');
		return false;
	}
}

function SendAutomatedEmail($to, $subject, $body){
	include_once './lib/mail.php';
	include_once './../PHP Admin Portal/vars/AdminInfo.php';
	print_debug('Sending mail to : '. $to);
	if( SendMail($to, $AutomatedEmailer, 'Automated_mailer', $subject, $body)){
		print_debug('Email sent : <br>' . $body);
		return true;
	}
	else{
		return false;
	}
}
?>