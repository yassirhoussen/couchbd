<?php

class User {
	
	protected  $id 			= NULL;
	private $lastName 		= NULL;
	private $firstName 		= NULL;
	private $address 		= NULL;
	private $furtherAddress = NULL;
	private $zipCode 		= NULL;
	private $town 			= NULL;
	private $email 			= NULL;
	private $password 		= NULL;
	
	//inner Object
	private $groups			= array();
	
	private $areObjects  = array('groups' => 'Group');
	
	public function __construct() {
		$this->id = uniqid();
	}
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}
	
	public function getLastName(){
		return $this->lastName;
	}

	public function setLastName($lastName){
		$this->lastName = $lastName;
	}

	public function getFirstName(){
		return $this->firstName;
	}

	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}

	public function getAddress(){
		return $this->address;
	}

	public function setAddress($adress){
		$this->address = $adress;
	}

	public function getFurtherAddress(){
		return $this->furtherAddress;
	}

	public function setFurtherAddress($furtherAddress){
		$this->furtherAddress = $furtherAddress;
	}

	public function getZipCode(){
		return $this->zipCode;
	}

	public function setZipCode($zipCode){
		$this->zipCode = $zipCode;
	}

	public function getTown(){
		return $this->town;
	}

	public function setTown($town){
		$this->town = $town;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}
	
	public function getGroups(){
		return $this->groups;
	}

	public function setGroups($groups){
		$this->groups[] = $groups;
	}

	public function getAreObjects(){
		return $this->areObjects;
	}

	public function setAreObjects($areObjects){
		$this->areObjects = $areObjects;
	}
}