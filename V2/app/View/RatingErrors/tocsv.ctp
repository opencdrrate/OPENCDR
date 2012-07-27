<?php
header('Content-Type: application/csv'); 
header('Content-Disposition: attachment; filename="cdr.csv"');
?>
callid,customerid,calltype,calldatetime,duration,direction,sourceip,originatingnumber,destinationnumber,lrn,cnamdipped,ratecenter,carrierid,wholesalerate,wholesaleprice,errormessage
<?php
	$i = 0;
	foreach ($callrecordmasterHelds as $callrecordmasterHeld):
		unset($callrecordmasterHeld['CallrecordmasterHeld']['rowid']);
		echo implode(',',$callrecordmasterHeld['CallrecordmasterHeld']);
		echo "\r\n";
	endforeach;
	
?>