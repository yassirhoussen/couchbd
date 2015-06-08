<?php

class Dao {
	
	private $database = "";
	private $url      = "";
	protected $couch  = "";
	
	public function __construct($database) {
		$this->database = $database;
		$path = $path = $_SERVER['DOCUMENT_ROOT'].'/';
		$config = require($path.'app/Config/config.php');
		$this->url      = $config['url'];
		$this->couch	= new couchDB($this->url);
		$this->init($database);
	}
	
	//########################## Database ##########################################
	protected function _createDatabase() {
		return $this->couch->createDatabase($this->database);
	}
	
	protected function _listDatabase() {
		return $this->couch->listDatabase();
	}
	
	protected function _deleteDatabase() {
		return $this->couch->deleteDatabase($this->database);
	}
	
	protected function getDatabaseInfo() {
		return $this->couch->getDatabaseInformation($this->database);
	}
	
	protected function databaseExist() {
		return $this->couch->databaseExist($this->database);
	}
	
	protected function _useDatabase($database) {
		return $this->couch->useDatabase($database);
	}
	//########################## Document ############################################
	protected function _create($object) {
		if(isset($object)) {
			$id = $object->getId();
			$array = $this->ObjectToArray($object);
			return $this->couch->storeDocument($array, $id);
		}
	}
	
	protected function _update($object) {
		if(isset($object)) {
			$id = $object->getId();
			$array = $this->ObjectToArray($object);
			return $this->couch->updateDocument($id, $array);
		}
	}
	
	protected function _readAll() {
		return $this->couch->getAllDocuments();
	}
	
	protected function _read($id, $type) {
		$object =  $this->couch->getDocument($id);
		return $this->_cast($type, $object->content);
	}
	
	protected function _delete($id) {
		return $this->couch->deleteDocument($id);	
	}
	
	//########################## Attachment ############################################
	
	protected function storeAttachment($path, $nameAttachment, $id) {
		$d = file_get_contents($path);
		return $this->couch->addAttachmentAsData($id, $d, $name); 
	}
	
	protected function getAttachmentUri($id, $nameAttachment) {
		return $couch->getAttachmentUri($id, $nameAttachment);
	}
	
	protected function getAllAttachmentUri($id) {
		return $couch->getAllAttachmentUri($id);
	}
	
	private function init($database) {
		if($this->couch->databaseExist($database))
			$this->couch->useDatabase($database);
		else {
			$this->couch->createDatabase($database);
			$this->couch->useDatabase($database);
		}
	}
	
	private function ObjectToArray($object) {
		$array = array();
	    $class = new ReflectionClass($object);
	    $properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);
	    foreach($properties as $property) {
		  	$key    = $property->getName();
			$value  = 'get'.ucfirst($key);
			$array[$key] = $object->$value();
		}
	    return $array;
	}


	private function _cast($destination, $sourceObject) {
	    if (is_string($destination)) {
	        $destination = new $destination();
	    }
	    $sourceReflection = new ReflectionObject($sourceObject);
	    $destinationReflection = new ReflectionObject($destination);
	    $sourceProperties = $sourceReflection->getProperties();
	    foreach ($sourceProperties as $sourceProperty) {
	        $sourceProperty->setAccessible(true);
	        $name = $sourceProperty->getName();
	        $value = $sourceProperty->getValue($sourceObject);
	        if ($destinationReflection->hasProperty($name)) {
	            $propDest = $destinationReflection->getProperty($name);
	            $propDest->setAccessible(true);
	            $propDest->setValue($destination,$value);
	        } else {
	            $destination->$name = $value;
	        }
	    }
    	return $destination;
	}
	
}