<?php
class WholesaleratesController extends AppController {

	var $name = 'Wholesalerates';

	function index() {
		$this->Wholesalerate->recursive = 0;
		$this->set('customers', $this->paginate());
	}
}
