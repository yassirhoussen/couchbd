<?php

class UserBL {
	
	private $userDao = NULL;
	private $type 	 = 'User';
	
	
	public function __construct() {
		$this->userDao = new DaoFactory('user', 'User');
		$this->userDao->setType($this->type);
	}
	
	public function create($object) {
		return $this->userDao->create($object);
	}
	
	public function userExist($email, $password) {
		$list 	= $this->userDao->getAll();
		$result = NULL;
		foreach($list as $elt) {
			if($elt->getEmail() == $email && $elt->getPassword() == $password) {
				return $elt;
			}
		}
		return false;
	}
	
	public function listAllUser() {
		return $this->userDao->getAll();
	}
}