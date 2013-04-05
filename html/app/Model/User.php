<?php
class User extends AppModel{
	var $name = 'User';
	var $primaryKey = 'id';
	var $useTable = 'users';
	
	public $belongsTo = array(
		'Customer' => array(
			'className' => 'Customer',
			'foreignKey' => 'customerid'
		)
	);
	
	public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            ),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'A user by this name already exists'
			)
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'customer')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );
	
	
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
}