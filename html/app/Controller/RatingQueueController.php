<?php
class RatingQueueController extends AppController {

	var $name = 'RatingQueue';
	var $paginate = array(
		'conditions' => array('calltype IS NOT NULL'),
		'limit' => 1000,
		'order' => array('CallrecordmasterTbr.calldatetime' => 'desc')
	);
	var $uses = array('CallrecordmasterTbr', 'Bandwidthcdr', 'Vitelitycdr', 'Thinktelcdr', 'AsteriskCDR');
	
	function index() {
		$this->CallrecordmasterTbr->recursive = 0;
		$this->set('callrecordmasterTbrs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Detail', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('callrecordmasterTbr', $this->CallrecordmasterTbr->read(null, $id));
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Detail', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->CallrecordmasterTbr->delete($id)) {
			$this->Session->setFlash(__('Detail deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Detail was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function tocsv(){
		$this->set('data', $this->CallrecordmasterTbr->find('all'));
		$this->layout = '';
	}
	
	function import(){
		if (!empty($this->data)) {
			$filename = $this->data['Document']['filename']['tmp_name'];
			$type = $this->data['CallrecordmasterTbr']['type'];
			if($type == 'bandwidth'){
				$this->Bandwidthcdr->import($filename, $type);
				$messages = $this->Bandwidthcdr->MoveToCdr($filename, $type);
				$this->Bandwidthcdr->query('TRUNCATE bandwidthcdr;');
			}
			else if($type == 'vitelity'){
				$this->Vitelitycdr->import($filename, $type);
				$messages = $this->Vitelitycdr->MoveToCdr($filename, $type);
				$this->Vitelitycdr->query('TRUNCATE vitelitycdr;');
			}
			else if($type == 'thinktel'){
				$this->Thinktelcdr->import($filename, $type);
				$messages = $this->Thinktelcdr->MoveToCdr($filename, $type);
				$this->Thinktelcdr->query('TRUNCATE thinktelcdr;');
			}
			else{
				$messages = $this->CallrecordmasterTbr->import($filename, $type);
			}
			$this->Session->setFlash(__($messages, true));
		}
	}
	
	function ratecalls($function, $progress, $max, $nextFunction = null){
		$this->layout = '';
		$this->CallrecordmasterTbr->RunStoredProcedure($function);
		if($nextFunction){
			$this->set('nextFunction', $nextFunction);
		}
		$this->set('progress', $progress);
		$this->set('max', $max);
		$this->set('function', $function);
	}
	
	function massimportcisco(){
		$CDRSourcePath = 'C:/a';
		$CDRProcessedPath = 'c:/b';
		
		$fileArray = scandir($CDRSourcePath);
		foreach($fileArray as $fileName){
			$fullFilePath = $CDRSourcePath . '/' .$fileName;
			$destinationFilePath = $CDRProcessedPath . '/' . $fileName;
			$fileType = filetype($fullFilePath);
			if($fileType == 'dir'){
				continue;
			}
			echo $fileName . ' : <br>';
			
			$fh = fopen($fullFilePath, 'r');
			$theData = fread($fh, filesize($fullFilePath));
			fclose($fh);
			$messages = $this->CallrecordmasterTbr->import($filename, 'cisco');
			if(rename($fullFilePath, $destinationFilePath)){ #move a file
				echo $fileName . ' moved to ' . $CDRProcessedPath . '<br>';
			}
			else{
				echo 'Failed to move : '.$filename . '<br>';
			}
		}
	}
}
