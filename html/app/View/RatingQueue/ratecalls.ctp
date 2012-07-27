<?php
$message = '';
if(isset($nextFunction)){
	$message = <<< HEREDOC
Running {$nextFunction}<br>
HEREDOC;
}
if($progress < $max){
	$message .= <<< HEREDOC
<progress value="{$progress}" max="{$max}"/>
HEREDOC;
	echo $message;
}
else{
	$message .= "Task Complete!";
	echo $message;
}
?>
