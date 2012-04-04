<?php
require_once('phpmailer/class.phpmailer.php');
function SendMail($to, $from, $from_name, $subject, $body) { 
	include "./vars/smtp_server.php";
	date_default_timezone_set('America/New_York');
	
	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = $host;
	$mail->Port = $port; 
	$mail->Username = $username;  
	$mail->Password = $password;           
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AddAddress($to);
	$mail->CharSet = 'UTF-8';
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
}

function SendMail_PayPal($to, $from, $from_name, $subject, $body, $footer, $link) { 
	include "./vars/smtp_server.php";
	date_default_timezone_set('America/New_York');
	
	global $error;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
	$mail->Host = $host;
	$mail->Port = $port; 
	$mail->Username = $username;  
	$mail->Password = $password;           
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;
	$mail->Body = $body.$link.$footer;
	$mail->AddAddress($to);
	$mail->CharSet = 'UTF-8';
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
}
?>