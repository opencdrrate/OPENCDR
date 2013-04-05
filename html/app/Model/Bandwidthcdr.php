<?php
class Bandwidthcdr extends AppModel{
	var $name = 'Bandwidthcdr';
	var $primaryKey = 'itemid';
	var $useTable = 'bandwidthcdr';
	var $actsAs = array('ImportCsv');
	
	function loadtype($line,$type){
	list($DEBTOR_ID,$itemid,$billable_minutes,$charge_name,$trans_rate,$AMOUNT,
$TAX_AMOUNT,$RECORD_DATE,$dest,$src,$type,$dest_lata,$dest_ocn,$dest_oocn,
$dest_rcs,$src_ocn,$src_oocn,$src_rcs,$lrn,$ipaddress,$billedtier,$rateddatetime,$cdrid) = str_getcsv($line,'|');
			$row = array();
			$row['debtor_id'] = $DEBTOR_ID;
			$row['itemid'] = $itemid;
			$row['billable_minutes'] = $billable_minutes;
			$row['itemtype'] = $type;
			$row['trans_rate'] = $trans_rate;
			$row['amount'] = $AMOUNT;
			$row['record_date'] = $RECORD_DATE;
			$row['src'] = $src;
			$row['dest'] = $dest;
			$row['dst_rcs'] = $dest_rcs;
			$row['lrn'] = $lrn;
			if(is_numeric($billable_minutes)){
				if($billable_minutes == 0){
					return false;
				}
				return $row;
			}
			else{
				return false;
			}
	}
	
	function MoveToCdr(){
		$numberofdetails = $this->find('count');
		$moveString = 'SELECT "fnMoveBandwidthCDRToTBR"();';
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$db->rawQuery($moveString);
		return $numberofdetails . ' items inserted.';
	}
}
?>