<?php

class VitelityAPI extends AppModel{
	var $name ='VitelityAPI';
	var $useTable = false;
	
	function Reset(){
	}
	
	function FetchCDR($login = '', $password = ''){
		$url = <<< HEREDOC
http://api.vitelity.net/api.php?login={$login}&pass={$password}&cmd=cdrlist&xml=yes
HEREDOC;
		
		$input = file_get_contents($url);
		$xml = Xml::build($input);
		// This converts the Xml document object to a formatted array
		//$xmlAsArray = Set::reverse($xml);
		// Or you can convert simply by calling toArray();
		$xmlAsArray = Xml::toArray($xml);
		$content = $xmlAsArray['content'];
		$status = $content['status'];
		if($status != 'ok'){
			echo $input . '<br>';
			return array();
		}
		$records = $content['records'];
		if(isset($records['call'])){
			$calls = $records['call'];
		}
		else{
			//empty recordset
			return array();
		}
			$cdrs = array();
			foreach($calls as $call){
				$date = $call['date'];
				$src = $call['src'];
				$dst = $call['dst'];
				$duration = $call['duration'];
				$cost = $call['cost'];
				
				$cdr = array();
				$cdr['CallrecordmasterTbr']['callid'] = 
										$date
										. '_' . $src
										. '_' . $dst
										. '_' . $duration;
				$cdr['CallrecordmasterTbr']['calldatetime'] = $date;
				$cdr['CallrecordmasterTbr']['originatingnumber'] = $src;
				$cdr['CallrecordmasterTbr']['destinationnumber'] = $dst;
				$cdr['CallrecordmasterTbr']['duration'] = $duration;
				$cdr['CallrecordmasterTbr']['wholesaleprice'] = $cost;
				$cdr['CallrecordmasterTbr']['carrierid'] = 'VITELITY';
				$cdrs[] = $cdr;
			}
		
		return $cdrs;
	}
}

?>