<?php

function GetEmail($customerid, $connectString){
	$db = pg_connect($connectString) or die('Unable to connect');
	$selectString = <<< HEREDOC
	SELECT primaryemailaddress
	FROM customercontactmaster WHERE customerid = '{$customerid}';
HEREDOC;
	
	$result = pg_query($db, $selectString);
	if($row = pg_fetch_assoc($result)){
		pg_close($db);
		return $row['primaryemailaddress'];
	}
	else{
		pg_close($db);
		return false;
	}
}

function SendNewPasswordNotification($username, $email, $newPassword){
	include 'lib/mail.php';
	include 'vars/AdminInfo.php';
	$body = <<< HEREDOC
You are receiving this notification because an administrator from {$Website} has reset your password.<p>

Your username : {$username}<br>
Your new password is : <b>{$newPassword}</b><p>

This is an automated message.  Do not reply to this email.<p>

Please login and change your password<p>

If you have any questions contact {$AdminEmail}
HEREDOC;
	return SendMail($email, $AutomatedEmailer, '', 'Password Changed!', $body);
}
?>
<?php

$javaScripts = <<< HEREDOC
<script type="text/javascript">
function confirmDelete(deleteid,customerid){
	var agree=confirm("Are you sure you want to delete this row?");
	if (agree){
		window.location = "customercontrolpanel.php?delete="+deleteid+"&customerid="+customerid;
	}
	else{	
	}
}
</script>
HEREDOC;
 ?>
<?php
include 'lib/Page.php';
include 'lib/encryption.php';
include 'config.php';
$customerid = $_GET['customerid'];

$message = '';
$table = '';
if(isset($_GET['passwordreset'])){
	$username = $_GET['username'];
	ResetPassword($username, $connectstring);
	$newPassword = RandomAlphaNum(5);
	$message .= 'Password reset<br>';
	ChangePassword($username, '', $newPassword, $connectstring);
	#$message .= 'New Password : '. $newPassword . '<br>';
	if($email = GetEmail($customerid, $connectstring)){
		if(SendNewPasswordNotification($username,$email, $newPassword)){
			$message .= 'Email notification sent to : ' . $email .'<br>';
		}
		else{
			$message .= 'WARNING : Email to : '. $email. 'failed to send.';
		}
	}
	else{
		$message .= 'WARNING : Unable to obtain an e-mail address from the database.<br>';
	}
}
if(isset($_GET['add'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	if($password != $password2){
		$message .= "The passwords don't match";
	}
	else{
		RegisterNewUser($username, $customerid, $password, $connectstring);
		$message .= "User added!";
	}
}
if(isset($_GET['delete'])){
	$username = $_GET['delete'];
	$db = pg_connect($connectstring);
	$deleteStatement = "DELETE FROM webportalaccess WHERE Username = '".$username."';";
	pg_query($db,$deleteStatement);
	pg_close($db);
}
if(isset($_GET['new'])){
	$table .= <<< HEREDOC
	<table>
	<form action=customercontrolpanel.php?customerid={$customerid}&add=1 method="POST">
		<tr><td>Username: </td><td><input type="text" name=username /></td>
		<tr><td>Password: </td><td><input type="password" name=password /></td>
		<tr><td>Repeat Password: </td><td><input type="password" name=password2 /></td>
		<tr><td><input type="submit" value="Submit"/></td><td></td>
	</form>
	</table>
HEREDOC;
}
else{
	$db = pg_connect($connectstring);
	$select_query = "SELECT Username FROM webportalaccess WHERE CustomerID = '" .$customerid. "'
					ORDER BY Username;";
	$result = pg_query($db, $select_query);
	$users = array();
	while($row = pg_fetch_assoc($result)){
		$users[] = $row;
	}
	pg_close($db);
	$table .= <<< HEREDOC
	<form action="customercontrolpanel.php?new=y&customerid={$customerid}" method="GET">
		<input type="hidden" name="customerid" value="{$customerid}"/>
		<input type="hidden" name="new" value="y"/>
		<input type="submit" class="btn blue add-customer" value="Create new username"/> 
	</form>
		<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">
		<tr><thead>
		<th>Username</th>
		<th></th>
		<th></th>
		</thead></tr>
		<tbody>
HEREDOC;
	if(count($users) == 0){
		# 'no users associated with this account'
		$table .= <<<HEREDOC
		<tr>
		<td>This user has no account</td>
		<td></td>
		</tr>
HEREDOC;
	}
	else{
		foreach($users as $userinfo){
			$table .= <<< HEREDOC
			<tr>
			<td>{$userinfo['username']}</td>
			<td>
	<form action="customercontrolpanel.php" method="GET">
		<input type="hidden" value="1" name="passwordreset"/>
		<input type="hidden" value="{$customerid}" name="customerid"/>
		<input type="hidden" value="{$userinfo['username']}" name="username"/>
		<input type="submit" value="Reset Password"/>
	</form>
			</td>
			<td><br>
<a href=javascript:confirmDelete('{$userinfo['username']}','{$customerid}') class="btn-action delete">Delete</a></td>
			</td>
			</tr>
HEREDOC;
		}
		$table .= <<< HEREDOC
		</tbody>
		<tfoot>
	    	<tr>
		    <td colspan="6"></td>
	    	</tr>
	    </tfoot></table>
HEREDOC;
	}
}
?>

<?php echo GetPageHead('Customer Control Panel','listcustomers.php',$javaScripts);?>
<div id="body">

<?php echo $message;?>
<?php echo $table;?>

</div>
<?php echo GetPageFoot('','');?>