<?php

class DaoFactory extends Dao implements IDAO {
	
	private $database = NULL;
	private $type	  = NULL;
	private $builder  = NULL;
	
	public function __construct($database, $type) {
		parent::__construct($database);
		$this->type = $type;
		// $this->builder = new Builder();
	}
	
	public function getAll() {
		$array = $this->_readAll();
		$result = array();
		foreach ($array as $element) {
			$id = $element->id;
			$result[] = $this->getOne($id);
		}
		return $result;
	}
	
	public function getOne($id){
		return $this->_read($id, $this->type);

	}
	
	public function create($object) {
		return $this->_create($object);
	}
	
	public function update($object){
		$this->_update($object);
	}
	
	public function getDatabase() {
		return $this->database;
	}
	
	public function setDatabase($database) {
		$this->database = $database;
		if ($this->databaseExist())
			$this->_useDatabase($database);
		else 
			$this->_createDatabase($database);
	}

	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	
}