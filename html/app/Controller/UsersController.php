<?php
class UsersController extends AppController {
	var $uses = array('User');
    var $components = array('Auth');

    public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login','logout', 'adminmissing'); // Letting users register themselves
	}
	
	public function login(){
		//Count number of administrators
		$count = $this->User->find('count', array(
			'conditions' => array('role' => 'admin')
			)
		);
		//If no administrators, redirect to an admin missing page.
		if($count == 0){
			$this->redirect(array('action'=>'adminmissing'));
		}
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}
	
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
	public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }
	
	public function adminmissing(){
		//Count number of administrators
		$count = $this->User->find('count', array(
			'conditions' => array('role' => 'admin')
			)
		);
		if($count > 0){
			$this->redirect(array('action'=>'login'));
		}
        if ($this->request->is('post')) {
            $this->User->create();
			$data = $this->request->data;
			$data['User']['role'] = 'admin';
            if ($this->User->save($data)) {
                $this->Session->setFlash(__('Administrator set!'));
				if ($this->Auth->login()) {
					$this->redirect($this->Auth->redirect());
				} else {
					$this->Session->setFlash(__('Invalid username or password, try again'));
				}
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
	}
}
?>