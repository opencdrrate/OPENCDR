<?php
class AdHocQueryController extends AppController{
	var $name = 'AdHocQuery';
	var $uses = array('Callrecordmaster');
	public function index(){
	}
	
	public function query(){
				
		$this->layout = '';
		if (!empty($this->data)) {
			$query = $this->data['AdHocQuery']['Query'];
			
			$results = $this->Callrecordmaster->query($query); 
			$this->set('results', $results);
			if(!stristr($query, 'select')){
				echo 'SQL command executed.<br>';
			}
		}
	}
}
?>