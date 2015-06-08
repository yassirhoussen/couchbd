<?php

class Builder {
	
	private $typeObject = NULL;
	
	public function __construct() {
	}
	
	public function createObject($listArray, $type) {
		$result = array();
		foreach($listArray as $value) {
			$result[] = $this->createOn($value->content, $type);
		}	
		return $result;
	}
	
	private function createOn($stdClass, $type) {
		return $this->objectToObject($stdClass, $type);
	}
	
	private function objectToObject($instance, $className) {
    return unserialize(sprintf(
        'O:%d:"%s"%s',
        strlen($className),
        $className,
        strstr(strstr(serialize($instance), '"'), ':')
    ));
}
	
}