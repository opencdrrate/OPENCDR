<?php
$path = $_SERVER["DOCUMENT_ROOT"]. '/Shared/';
include_once $path . 'lib/Page.php';
include_once $path . 'lib/vi_did.php';
include_once $path . 'vars/voip_login_info.php';
include_once $path . 'DAL/table_didmaster.php';
include_once $path . 'conf/ConfigurationManager.php';
$manager = new ConfigurationManager();
$connectstring = $manager->BuildConnectionString();
$content = '';
$error = '';

if(isset($_GET['function'])){
	$function = $_GET['function'];
	if($function = 'showAssignResult'){
		$table = new psql_didmaster($connectstring);
		$customerid = $_POST['customerid'];
		$fail = false;
		if(isset($_POST['epg'])){
			$epg = $_POST['epg'];
		}
		else{
			$error = 'Error : epg not set';
			$fail = true;
		}
		if(isset($_POST['tnList'])){
			$tnArray = $_POST['tnList'];
		}
		else{
			$error = 'Error : No dids selected';
			$fail = true;
		}
		if( $epg == '' ){
			$error = 'Error : epg not set';
			$fail = true;
		}
		if( empty($customerid) ){
			$error = 'Error : customerid not set';
			$fail = true;
		}
		if(!$fail){
			$client = new VI_Client($vi_user,$vi_pass);
			$resultList = $client->assignDIDs($tnArray, $epg);
			$successList = array();
			$content .= <<<HEREDOC
			<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0"><thead><tr>
			<th>Telephone number</th>
			<th>Status</th>
			<th>StatusCode</th>
			<th>Result</th>
			</thead></tr>
HEREDOC;
			$table->Connect();
			foreach($resultList as $did){
				$insertResult = false;
				$insertMessage = '';
				if($did['statusCode'] == 100 || $did['statusCode'] == '100' ){
					$insertResult = $table->Insert(array('did' => $did['tn'], 'customerid' => $customerid));
				}
				if($insertResult){
					$insertMessage = 'DID <font color="green">added</font> to customer : ' . $customerid;
				}
				else{
					$insertMessage = 'DID <font color="red">not added</font> to customer : ' . $customerid;
				}
				$content .= <<< HEREDOC
				<tr>
				<td>{$did['tn']}</td>
				<td>{$did['status']}</td>
				<td>{$did['statusCode']}</td>
				<td>{$insertMessage}</td>
				</tr>
HEREDOC;
			}
			$table->Disconnect();
			$content .= <<< HEREDOC
			</table>
HEREDOC;
		}
	}
}
?>

<?php echo GetPageHead('Add VOIP Innovations DID','listdids.php');?>
<div id="body">
<?php echo $error;?>
<?php echo $content;?>
</div>
<?php echo GetPageFoot();?>