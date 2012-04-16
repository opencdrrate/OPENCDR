<?php
include_once 'lib/Page.php';
include_once 'lib/session.php';
include_once 'vars/config.php';

if(!isset($_GET['token'])){
	#You need to be logged in to view this page
}
$token = $_GET['token'];

if(IsTokenExpired($token, $connectstring)){
	header('location: login.php?error=notloggedin');
}
else{
	UpdateExpiry($token,$connectstring);
}

$content = <<< HEREDOC
	<a href="login.php?token={$token}&logout=1">logout</a>
HEREDOC;
?>
<?php echo GetPageHead('Main', '');?>
<div id='body'>
<?php echo $content;?>
</div>
<?php echo GetPageFoot();?>