<?php

class Group {
	
	protected $id  = NULL;
	private $name  = NULL;
	private $right = NULL;
	
	public function __construct() {
		$this->id = uniqid();
	}
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getRight(){
		return $this->right;
	}

	public function setRight($right){
		$this->right = $right;
	}
	
}

