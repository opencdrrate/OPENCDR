<?php
include_once 'lib/passwordrecovery.php';
include_once 'vars/config.php';
include_once 'lib/Page.php';
$errors = '';
$content = '';

if(isset($_GET['token'])){
	$token = $_GET['token'];
	#check if token exists
	if(VerifyToken($token,$connectstring)){
		#continue
	}
	else{
		#if not, send an error
	}
}
else{
	#redirect somewhere else
}
?>

<?php echo GetPageHead('Password Recovery Page','login.php');?>
<div id="body">
<?php echo $errors;?><br>
<?php echo $content;?><br>
<form action="login.php" method="POST">
<table>
<tr><td>New Password : </td><td><input type="password" name="pwd"/></td></tr>
<tr><td>Confirm Password : </td><td><input type="password" name="confirm"/></td></tr>
<input type="hidden" name="func" value="resetpwd">
<input type="hidden" name="token" value="<?php echo $token;?>"/>
<tr><td><input type="submit" value="Reset Password"/></td><td></td></tr>
</table>
</form>
</div>
<?php echo GetPageFoot();?>