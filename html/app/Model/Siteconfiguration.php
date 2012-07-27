<?php
class Siteconfiguration extends AppModel {
	var $name = 'Siteconfiguration';
	var $useTable = 'systemsettings_string';
	var $primaryKey = 'settingname';
	//var $filepath = 'C:\wamp\www\Cake\app\models\datasources\site.conf';
	
	function ListAll(){
		$settings = $this->find('list',array('fields'=>array('settingname','settingvalue')));
		$settings['currencysettings'] = array('after'=>false, 'negative' => '-');
		if(!isset($settings['currency'])){
			$settings['currency'] = 'USD';
		}
		if(!isset($settings['city'])){
			$settings['city'] = '';
		}
		if(!isset($settings['state'])){
			$settings['state'] = '';
		}
		if(!isset($settings['postal'])){
			$settings['postal'] = '';
		}
		if(!isset($settings['country'])){
			$settings['country'] = '';
		}
		if(!isset($settings['companyname'])){
			$settings['companyname'] = '';
		}
		return $settings;
	}
	
	function WriteSettings($settings){
		foreach($settings as $name => $setting){
			if($this->find('first', array('conditions' => array('settingname' => $name)))){
				$this->create();
				$this->set('settingname', $name);
				$this->set('settingvalue', $setting);
				$this->save();
			}
			else{
				$this->create();
				$this->set('settingname', $name);
				$this->set('settingvalue', $setting);
				$this->save();
			}
		}
		return true;
	}
	
	function RegionalizationSettings(){
		$settings = $this->find('list',array('fields'=>array('settingname','settingvalue')));
		
		return $settings;
	}
}
?>