<?php
include_once 'config.php';
include_once $path . 'lib/Page.php';
include_once $path . 'conf/ConfigurationManager.php';
include_once $path . 'lib/psql_connection.php';

$manager = new ConfigurationManager();

if(isset($_GET['save'])){
	$manager->ChangeSetting('sitename', $_POST['sitename']);
	$manager->ChangeSetting('host', $_POST['host']);
	$manager->ChangeSetting('port', $_POST['port']);
	$manager->ChangeSetting('dbname', $_POST['dbname']);
	$manager->ChangeSetting('user', $_POST['user']);
	$manager->ChangeSetting('password', $_POST['password']);
	$manager->ChangeSetting('smtp_host', $_POST['smtp_host']);
	$manager->ChangeSetting('smtp_port', $_POST['smtp_port']);
	$manager->ChangeSetting('smtp_username', $_POST['smtp_username']);
	$manager->ChangeSetting('smtp_password', $_POST['smtp_password']);
	$manager->ChangeSetting('admin_email',$_POST['admin_email']);
	$manager->ChangeSetting('auto_email',$_POST['auto_email']);
	$manager->ChangeSetting('website',$_POST['website']);
	$manager->ChangeSetting('company_name',$_POST['company_name']);
	$manager->ChangeSetting('company_address1',$_POST['company_address1']);
	$manager->ChangeSetting('company_address2',$_POST['company_address2']);
	$manager->ChangeSetting('company_country',$_POST['company_country']);
	$manager->ChangeSetting('logo',$_POST['logo']);

	$manager->ChangeSetting('voip_user',$_POST['voip_user']);
	$manager->ChangeSetting('voip_pass',$_POST['voip_pass']);
	$manager->ChangeSetting('region',$_POST['region']);
	$manager->Save();
}
$connectstring = $manager->BuildConnectionString();

$sitename = $manager->GetSetting('sitename');
$host = $manager->GetSetting('host');
$port = $manager->GetSetting('port');
$dbname = $manager->GetSetting('dbname');
$user = $manager->GetSetting('user');
$password = $manager->GetSetting('password');

$smtp_host = $manager->GetSetting('smtp_host');
$smtp_port = $manager->GetSetting('smtp_port');
$smtp_username = $manager->GetSetting('smtp_username');
$smtp_password = $manager->GetSetting('smtp_password');

$admin_email = $manager->GetSetting('admin_email');
$auto_email = $manager->GetSetting('auto_email');
$website = $manager->GetSetting('website');

$company_name = $manager->GetSetting('company_name');
$company_address1 = $manager->GetSetting('company_address1');
$company_address2 = $manager->GetSetting('company_address2');
$company_country = $manager->GetSetting('company_country');
$logo = $manager->GetSetting('logo');

$voip_user = $manager->GetSetting('voip_user');
$voip_pass = $manager->GetSetting('voip_pass');

$region = $manager->GetSetting('region');

$testConnect = new psql_connection();
$isValid = $testConnect->TestConnectstring($connectstring);
$isConnectedMsg = '';
if($isValid){
	$isConnectedMsg = '<font color="Green">Connection successful!</font>';
}
else{
	$isConnectedMsg = '<font color="Red">'.$testConnect->error.'</font>';
}

$javascripts = <<< HEREDOC
HEREDOC;

?>

<?php echo GetPageHead("Configuration Manager", "main.php"); ?>

<div id="body">

<form action="configurationpage.php?save=1" method="POST">
<label for="sitename">SiteName: </label>	<input type="text" id="sitename" name="sitename" value="<?php echo $sitename;?>" /><br>
<div style="border:1px solid black;">
<strong>Postgres Settings</strong><br>
<label for="host">Host: </label>			<input type="text" id="host" name="host" value="<?php echo $host;?>" /><br>
<label for="port">Port:	</label>			<input type="text" id="port" name="port" value="<?php echo $port;?>" /><br>
<label for="dbnname">Database:	</label>	<input type="text" id="dbnname" name="dbname" value="<?php echo $dbname;?>" /><br>
<label for="user">User:	</label>			<input type="text" id="user" name="user" value="<?php echo $user;?>" /><br>
<label for="password">Password:	</label>	<input type="password" id="password" name="password" value="<?php echo $password;?>" /><br>
<br>
<?php echo $isConnectedMsg;?>
</div>

<div style="border:1px solid black;">
<strong>SMTP Settings</strong><br>
<label for="smtp_host">smtp_host: </label>			<input type="text" id="smtp_host" name="smtp_host" value="<?php echo $smtp_host;?>" /><br>
<label for="smtp_port">smtp_port:	</label>		<input type="text" id="smtp_port" name="smtp_port" value="<?php echo $smtp_port;?>" /><br>
<label for="smtp_username">smtp_username:	</label>	<input type="text" id="smtp_username" name="smtp_username" value="<?php echo $smtp_username;?>" /><br>
<label for="smtp_password">smtp_password:	</label>			<input type="password" id="user" name="smtp_password" value="<?php echo $smtp_password;?>" /><br>
</div>

<div style="border:1px solid black;">
<strong>Administration Information</strong><br>
<label for="admin_email">E-Mail: </label>			<input type="text" id="admin_email" name="admin_email" value="<?php echo $admin_email;?>" /><br>
<label for="auto_email">AutomatedEmailer:	</label>		<input type="text" id="auto_email" name="auto_email" value="<?php echo $auto_email;?>" /><br>
<label for="website">Website:	</label>	<input type="text" id="website" name="website" value="<?php echo $website;?>" /><br>
</div>

<div style="border:1px solid black;">
<strong>Voip Innovations Login info</strong><br>
<label for="smtp_host">Voip Username: </label>			<input type="text" id="voip_user" name="voip_user" value="<?php echo $voip_user;?>" /><br>
<label for="smtp_port">Voip Password:	</label>		<input type="password" id="voip_pass" name="voip_pass" value="<?php echo $voip_pass;?>" /><br>
</div>
<div style="border:1px solid black;">
<strong>Localization Settings</strong><br>
<label for="smtp_host">Region: </label>
<select name="region" id="region">
<option value="CAD" <?php if($region == 'CAD'){echo 'selected="selected"';}?>>CAD</option>
<option value="USD" <?php if($region == 'USD'){echo 'selected="selected"';}?>>USD</option>
<option value="EUR" <?php if($region == 'EUR'){echo 'selected="selected"';}?>>EUR</option>
</select><br>

</div>
<div style="border:1px solid black;">
<strong>Company Information</strong><br>
<label for="company_name">CompanyName: </label>			<input type="text" id="company_name" name="company_name" value="<?php echo $company_name;?>" /><br>
<label for="company_address1">CompanyAddress1:	</label>		<input type="text" id="company_address1" name="company_address1" value="<?php echo $company_address1;?>" /><br>
<label for="company_address2">CompanyAddress2:	</label>	<input type="text" id="company_address2" name="company_address2" value="<?php echo $company_address2;?>" /><br>
<label for="company_country">CompanyCountry:	</label>	<input type="text" id="company_country" name="company_country" value="<?php echo $company_country;?>" /><br>
<label for="logo">Logo:	</label>	<input type="text" id="logo" name="logo" value="<?php echo $logo;?>" /><br>
</div>

<input type="submit" value="Save Settings" />
</form>
<br>
</div>
<?php echo GetPageFoot();?>