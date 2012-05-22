<?php

include_once 'config.php';
	include_once $path . 'lib/Page.php';
	include_once $path . 'lib/mail.php';
	include_once $path . 'paypal.php';
	include_once $path . 'conf/ConfigurationManager.php';
	$manager = new ConfigurationManager();
	$connectstring = $manager->BuildConnectionString();

if (isset($_POST['submit'])) {	

	$amount = $_POST['amount'];
	$email = $_POST['email'];

	$link = <<<HEREDOC
<a href='https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=$business&item_name=$itemname&item_number=test&amount=$amount&currency_code=$currencycode&button_subtype=services&no_note=0&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest'>Make a Payment</a>
HEREDOC;

	$bool = SendMail_PayPal($email, $fromemail, $fromname, $subject, $body, $footer, $link);

	if (!$bool == true)
	{

		echo ("<script type='text/javascript'>");
		echo ("alert($error);");
		echo ("</script>");

	} else {

		echo ("<script type='text/javascript'>");
		echo ('var answer = confirm("Payment request has been sent.");');
		echo ("if ((!answer) || (answer))");
		echo ('window.location = "listcustomers.php";');
		echo ('</script>');

	}

}
else
{

	$customerid = $_GET['customerid'];

	$db = pg_connect($connectstring);

	$query = "SELECT primaryemailaddress FROM customercontactmaster where customerid = '$customerid';";
	$result = pg_query($query) or die(print pg_last_error());
	$customeremail = pg_fetch_result($result, 0, 0);

	pg_close($db); 
}
	

?>

<?php echo GetPageHead("PayPal Payment Request", "listcustomers.php");?>
<div id="body">

    <form name="requestform" id="standardform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <label>Amount:</label><input type="text" name="amount" value="<?php if (isset($_POST['submit'])) { echo $amount; } ?>" ><br />
      <label>Email Address:</label><input type="text" value="<?php if (isset($_POST['submit'])) { echo $email; } else { echo $customeremail; } ?>" name="email"><br />
      <input type="submit" name="submit" value="Send PayPal Request">	
    </form> 
    
</div>
<?php echo GetPageFoot("","");?>
