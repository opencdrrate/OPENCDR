<?php
class UsersController extends AppController {
	var $uses = array('User', 'Customer');
    var $components = array('Auth');

    public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login','logout', 'adminmissing'); // Letting users register themselves
	}
	public function index(){
		$this->set('users', $this->paginate());
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
		$this->set('customers', $this->Customer->find('list'));
        if ($this->request->is('post')) {
			$data = $this->request->data;
			if($data['User']['userrole'] == 'admin'){
				$data['User']['role'] = 'admin';
			}
			else{
				$data['User']['role'] = 'customer';
				$data['User']['customerid'] = $data['User']['userrole'];
			}
			if($data['User']['confirm'] != $data['User']['password']){
				$this->Session->setFlash(__('Confirmation password doesn\'t match password.  Please try again'));
                $this->redirect(array('action' => 'add'));
			}
            $this->User->create();
            if ($this->User->save($data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            }
			else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }
	
	public function adduser($customerid) {
		$this->set('customerid', $customerid);
        if ($this->request->is('post')) {
			$data = $this->request->data;
			if($data['User']['confirm'] != $data['User']['password']){
				$this->Session->setFlash(__('Confirmation password doesn\'t match password.  Please try again'));
                $this->redirect(array('action' => 'add'));
			}
            $this->User->create();
            if ($this->User->save($data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array('action' => 'index'));
            }
			else {
				print_r($data);
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }
	public function edit($id = null){
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	public function newpwd($id = null){
		if (!empty($this->data)) {
			if($this->data['User']['confirm'] != $this->data['User']['password']){
				$this->Session->setFlash(__('Confirmation password doesn\'t match password.  Please try again'));
                $this->redirect(array('action' => 'add'));
			}
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('Password changed', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('ERROR: Password not changed, please try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}
	
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for server', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id, true)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
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