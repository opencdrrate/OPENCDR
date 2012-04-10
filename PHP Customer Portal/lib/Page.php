<?php

function GetPageHead($title = "", $backlink = "login.php", $head = ''){
$head = <<< HEREDOC
<!DOCTYPE HTML>
<html lang="en">
<head>

<!-- 

--======================================================================--
    OpenCDRRate – Rate your call records.
    Copyright (C) 2012  DTH Software, Inc

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    See <http://www.gnu.org/licenses/>.                                 
--======================================================================-- 
-->

    <meta charset="UTF-8">
    <title>{$title}</title>
	{$head}
    <!-- Stylesheet -->
    <link rel="stylesheet" type="text/css" media="screen" href="stylesheets/style.css" />
	
</head>
    <body>
    <img id="bg" alt="" src="images/bg.jpg" />
	
	<div id="header">
		<a href="{$backlink}" class="back">&lt; Back</a>
    	<h1 class="title">{$title}</h1>
    </div>
HEREDOC;
	return $head;
}

function GetPageFoot($termsLink = "", $privacyLink = ""){
	$foot = <<< HEREDOC
	
    <div id="footer" style="position: fixed; bottom: 0;">
    	<p class="copy">Copyright &copy; 2012 DTH Software, Inc. All rights reserved.</p>
	<p class="legal">
	    <a href="/main.php">Home</a>
	    <a href="{$termsLink}"> Terms of Use</a>
	    <a href="{$privacyLink}">Privacy Policy</a>	
	</p>
    </div>
    </body> 
</html>
HEREDOC;
return $foot;
}
?>