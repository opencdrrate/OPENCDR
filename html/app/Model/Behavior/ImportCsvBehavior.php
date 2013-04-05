<?php
class ImportCsvBehavior extends ModelBehavior{
	function import(&$model, $filename, $type){
		$lines = file($filename);
		$numberOfSaved = 0;
		foreach($lines as $line){
			//Convert data from filetype to appropriate fields
			$data = $model->loadtype($line, $type);
			$model->Create();
			if(empty($data)){
				continue;
			}
			if($model->save($data)){
				$numberOfSaved++;
			}
		}
		
		return  $numberOfSaved . ' items inserted.';
	}
}
?>