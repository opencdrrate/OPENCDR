<?php
include_once 'vars/config.php';
include_once 'lib/Page.php';
include_once 'lib/session.php';
include_once 'lib/passwordrecovery.php';
include_once 'lib/encryption.php';

$errors = '';
$content = '';
function customError($errno, $errstr)
{
	global $errors;
	$errors .= '<font color="red">'.$errstr.'</font>';
}
set_error_handler("customError");

if(isset($_GET['error'])){
	$errType = $_GET['error'];
	if($errType='notloggedin'){
		trigger_error('Your session expired.  Please login again.');
	}
}

if(isset($_POST['func'])){
	$func = $_POST['func'];
	if($func == 'resetpwd'){
	
		$token = $_POST['token'];
		$newPassword = $_POST['pwd'];
		$confirm = $_POST['confirm'];
		if($confirm == $newPassword){
			if($username = VerifyToken($token, $connectstring)){
				ResetPassword($username, $connectstring);
				ChangePassword($username, '', $newPassword, $connectstring);
				DeleteToken($username,$connectstring);
			}
		}
		else{
			$error = <<< HEREDOC
		<font color="red">Your new password doesn't match.  Click on the link provided in your confirmation e-mail to try again.</font><br>
HEREDOC;
		}
	}
}

if(isset($_GET['login'])){
	if(!isset($_POST['user'])){
		#error
		trigger_error('Enter your username');
	}
	if(!isset($_POST['pwd'])){
		#error
		trigger_error('Please enter your password');
	}
	$username = $_POST['user'];
	$password = $_POST['pwd'];
	
	if(ValidateUsernamePassword($username,$password,$connectstring)){
		#success
		$token = BeginSession($username, $connectstring);
		header('location: main.php?token='.$token);
	}
	else{
		#user not found or incorrect password
		trigger_error('User not found or incorrect password');
	}
}
if(isset($_GET['logout'])){
	$token = $_GET['token'];
	EndSession($token, $connectstring);
	$content .= '<font color="red">logged out</font><br>';
}
?>

<?php echo GetPageHead('Login Page','login.php');?>
<div id="body">
<?php echo $errors;?>
<?php echo $content;?>
<form action="login.php?login=1" method="POST">
<table>
<tr><td>Username : </td><td><input type="text" name="user"/></td></tr>
<tr><td>Password : </td><td><input type="password" name="pwd"/></td></tr>
<tr><td><input type="submit" value="Login"/></td><td></td></tr>
</table>
</form><br>
<a href="forgotpassword.php">Forgot password?</a>
</div>
<?php echo GetPageFoot();?>