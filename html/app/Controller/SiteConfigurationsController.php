<?php
class SiteConfigurationsController extends AppController{
	var $name = 'SiteConfigurations';
	var $uses = array('Siteconfiguration');
	public function index(){
		$settings = $this->Siteconfiguration->ListAll();
		$data = array();
		$data['SiteConfigurations'] = $settings;
		$this->data = $data;
	}
	
	public function save(){
		if (!empty($this->data)) {
			
			try{
				if($this->Siteconfiguration->WriteSettings($this->data['SiteConfigurations'])){
					$this->Session->setFlash('Settings saved!');
					$this->redirect('/');
				}
				else{
					$this->Session->setFlash('Settings not saved.');
					$this->redirect(array('action' => 'index'));
				}
			}
			catch(Exception $e){
				$this->Session->setFlash($e->GetMessage());
				$this->redirect(array('action' => 'index'));
			}
		}
		else{
			$this->redirect(array('action' => 'index'));
		}
		
	}
}
?>