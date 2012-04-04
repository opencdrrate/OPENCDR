<?php
include 'lib/mail.php';
$result = '';
if(isset($_POST['send'])){
	$name		= $_POST['name'];
	$to			= $_POST['to'];
	$from		= $_POST['from'];
	$subject	= $_POST['subject'];
	$message	= $_POST['message'];
	$returnCode = SendMail($to, $from,$name, $subject, $message);
	if($returnCode == 1){
		$result .= "<font color=\"FF0000\">MAIL SENT</font><br>";
	}
	else{
		$result .= "<font color=\"FF0000\">MAIL NOT SENT</font><br>";
	}
}
?>

<?php echo $result;?>
<table>
<form action="testsmtp.php" method="post" id="standardform">
<input type=hidden name=send value=1/>
<tr><td>From :		</td><td><input type=text name="from"></td></tr>
<tr><td>To :		</td><td><input type=text name="to"></td></tr>
<tr><td>Your Name :	</td><td><input type=text name="name"></td></tr>
<tr><td>Subject : 	</td><td><input type=text name="subject"></td></tr>
<tr><td>Write your email here: </td></tr>
</table>
<textarea name="message" cols=40 rows=6 /></textarea>
<p><input type="submit" value="Send Email"/>
</form>