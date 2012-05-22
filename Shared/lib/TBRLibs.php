<?php
$path = '../';
include_once $path . 'DAL/table_callrecordmaster_tbr.php';
include_once $path . 'DAL/table_thinktelcdr.php';	
include_once $path . 'DAL/table_vitelitycdr.php';
include_once $path . 'DAL/table_bandwidthcdr.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();

/*
		$assocItem = array();
		$assocItem['callid'] = $item[];
		$assocItem['customerid'] = $item[];
		$assocItem['calltype'] = $item[];
		$assocItem['calldatetime'] = $item[];
		$assocItem['duration'] = $item[];
		
		$assocItem['direction'] = $item[];
		$assocItem['sourceip'] = $item[];
		$assocItem['originatingnumber'] = $item[];
		$assocItem['destinationnumber'] = $item[];
		$assocItem['lrn'] = $item[];
		
		$assocItem['cnamdipped'] = $item[];
		$assocItem['ratecenter'] = $item[];
		$assocItem['carrierid'] = $item[];
		$assocItem['wholesalerate'] = $item[];
		$assocItem['wholesaleprice'] = $item[];
		
	   */


if(isset($_SERVER['HTTP_TYPE'])){

	$type = $_SERVER['HTTP_TYPE'];
	#$myFile = $_SERVER['HTTP_X_FILENAME'];

	$myFile = 'php://input';
	header('Content-Type: text/html');
	header('Content-Length: ' . $_SERVER['HTTP_X_FILESIZE']);
	
	if($type == 'bandwidth'){
		#echo ProcessBandwidth($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/BandwidthFileImporter.php';
		$table = new psql_bandwidthcdr($connectstring);
		$fileImporter = new BandwidthFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll('|', false);
		$fileImporter->Close();
		
		$table->Connect();
		$table->MoveToTBR();
		$table->Disconnect();
	}
	else if($type == 'asterisk'){
		#echo ProcessAsterisk($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/AsteriskFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new AsteriskFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'itel'){
		include_once $path . 'lib/FileUtils/iTelFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new iTelFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'vitelity'){
		#echo ProcessVitelity($myFile, $connectstring);
		
		include_once $path . 'lib/FileUtils/VitelityFileImporter.php';
		$table = new psql_vitelitycdr($connectstring);
		$fileImporter = new VitelityFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
		
		$table->Connect();
		$table->MoveToTBR();
		$table->Disconnect();
	}
	else if($type == 'thinktel'){
		#echo ProcessThinktel($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/ThinktelFileImporter.php';
		$table = new psql_thinktelcdr($connectstring);
		$fileImporter = new ThinktelFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
		
		$table->Connect();
		$table->MoveToTBR();
		$table->Disconnect();
	}
	else if($type =='aretta'){
		#echo ProcessAretta($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/ArettaFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new ArettaFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type =='voip'){
		#echo ProcessVOIP($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/VoipFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new VoipFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(';', false);
		$fileImporter->Close();
	}
	else if($type == 'cisco'){
		#echo ProcessCisco($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/CiscoFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new CiscoFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'slinger'){
		#echo ProcessSlinger($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/SlingerFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new SlingerFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'telepo'){
		#echo ProcessTelepo($myFile, $connectstring);
		
		include_once $path . 'lib/FileUtils/TelepoFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new TelepoFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'nextone'){
		#echo ProcessNextOne($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/NextoneFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new NextoneFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(';', false);
		$fileImporter->Close();
	}
	else if($type == 'telastic'){
		#echo ProcessTelastic($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/TelasticFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new TelasticFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'netsapiens'){
		#echo ProcessNetSapiens($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/NetsapiensFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new NetsapiensFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(',', false);
		$fileImporter->Close();
	}
	else if($type == 'sansay'){
		#echo ProcessSansay($myFile, $connectstring);
		include_once $path . 'lib/FileUtils/SansayFileImporter.php';
		$table = new psql_callrecordmaster_tbr($connectstring);
		$fileImporter = new SansayFileImporter($table);
		$fileImporter->Open($myFile);
		$fileImporter->ImportAll(';', false);
		$fileImporter->Close();
	}
}
?>