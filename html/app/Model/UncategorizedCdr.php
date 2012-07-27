<?php
class UncategorizedCdr extends AppModel {
	var $name = 'UncategorizedCdr';
	var $useTable = 'callrecordmaster_tbr';
	var $primaryKey = 'rowid';
	var $displayField = 'calldatetime';
}