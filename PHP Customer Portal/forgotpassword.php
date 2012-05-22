<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';

$errors = '';
function customError($errno, $errstr)
{
	global $errors;
	$errors .= '<font color="red">'.$errstr.'</font><br>';
}
set_error_handler("customError");


	$content = <<< HEREDOC
	Please type in your e-mail address and we'll send you a notification to confirm your new password.
HEREDOC;

if(isset($_GET['email'])){
	$email = $_GET['email'];
	$username = $_GET['username'];
	include_once $path . 'vars/config.php';
	include_once $path . 'lib/emailcontact.php';
	include_once $path . 'lib/encryption.php';
	include_once $path . 'lib/passwordrecovery.php';
	
	$hasEmail = EmailExists($email, $connectstring);
	$hasUsername = GetCustomerID($username, $connectstring);
	$userHasEmail = $hasEmail == $hasUsername;
	
	if(($hasEmail == false )or ($hasUsername == false )or ($userHasEmail == false)){
		#if not print an error
		if(!$hasEmail){
			trigger_error('No email address found.');
		}
		else if(!$hasUsername){
			trigger_error('Username not found.');
		}
		else if(!$userHasEmail){
			trigger_error('Incorrect email or username entered.');
		}
	}
	else{
		#clear table
		DeleteToken($username,$connectstring);
		date_default_timezone_set('America/New_York');
		#on successful 
		# generate and insert a random number / expiry into the database
		$token = MakeNewToken($username, $connectstring);
		$link = $customerpublicdomain . 'passwordreset.php?token='.$token;
		#send an email with hash code then go to notification page
		$body = <<< HEREDOC
		You're being sent this because you forgot your password.<br>
		
		Click on this link to reset and change your password.<br>
		{$link}
HEREDOC;
		SendAutomatedEmail($email, 'Password Retrieval', $body);
	}
	
}
?>

<?php echo GetPageHead('Password Recovery Page','login.php');?>
<div id="body">
<?php echo $errors;?><br>
<?php echo $content;?>
<form action="forgotpassword.php" method="GET">
<table>
<tr><td>Username : </td><td><input type="text" name="username"/></td></tr>
<tr><td>E-mail : </td><td><input type="text" name="email"/></td></tr>
<tr><td><input type="submit" value="Send E-mail"/></td><td></td></tr>
</table>
</form>
</div>
<?php echo GetPageFoot();?>