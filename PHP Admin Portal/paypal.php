<?php

#paypal link variables ---
$business = 'dhansen%40dthsoftware%2ecom';
$itemname = 'test';
$currencycode = 'USD';

#email variables ---
$fromemail = "dthsoftware69@gmail.com";
$fromname = "God";
$subject = "PayPal Payment Request";

$body = <<<HEREDOC
<html>
<h4>Hi There,</h4>
<p>Below you will find a link to a paypal website that will allow you to make a payment to us. If you have any question, please feel free to contact us at: NO-ONE-CARES.com.</p><br/>
HEREDOC;
 
$footer = <<<HEREDOC
<br/>
<p>DTH Software Inc.<p>
</html>
HEREDOC;


?>