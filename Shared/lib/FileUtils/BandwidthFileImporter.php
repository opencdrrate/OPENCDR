<?php
include_once 'AbstractFileImporter.php';
class BandwidthFileImporter extends AbstractFileImporter{
	function BandwidthFileImporter($table){
		$this->sqlTable = $table;
	}
	function ParseLine($data){
	/*DEBTOR_ID|item.id|computed.billable_minutes|computed.charge_name|computed.trans_rate|AMOUNT|
TAX_AMOUNT|RECORD_DATE|item.dest|item.src|item.type|item.dest_lata|item.dest_ocn|item.dest_oocn|
item.dest_rcs|item.src_ocn|item.src_oocn|item.src_rcs|lrn|ipaddress|billedtier|rateddatetime|cdrid*/

		list($DEBTOR_ID,$itemid,$billable_minutes,$charge_name,$trans_rate,$AMOUNT,
$TAX_AMOUNT,$RECORD_DATE,$dest,$src,$type,$dest_lata,$dest_ocn,$dest_oocn,
$dest_rcs,$src_ocn,$src_oocn,$src_rcs,$lrn,$ipaddress,$billedtier,$rateddatetime,$cdrid) = $data;
			/*
			$bandwidthMap['DEBTOR_ID'] = 'debtor_id';
$bandwidthMap['item.id'] = 'itemid';
$bandwidthMap['computed.billable_minutes'] = 'billable_minutes';
$bandwidthMap['item.type'] = 'itemtype';
$bandwidthMap['computed.trans_rate'] = 'trans_rate';
$bandwidthMap['AMOUNT'] = 'amount';
$bandwidthMap['RECORD_DATE'] = 'record_date';
$bandwidthMap['item.src'] = 'src';
$bandwidthMap['item.dest'] = 'dest';
$bandwidthMap['item.dst_rcs'] = 'dst_rcs';
$bandwidthMap['lrn'] = 'lrn';
			*/
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
		$oldParams = array('itemid' => $row['itemid']);
			
		return array('oldParams' => $oldParams, 'newParams'=>$row);
	}
}
?>