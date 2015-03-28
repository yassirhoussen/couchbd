<?php
/*
 Copyright (C) 2015 Yassir Houssen Abdullah
 this work is based on https://github.com/dready92/PHP-on-Couch

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Lesser General Public License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'lib/couch.php';
require_once 'lib/couchClient.php';
require_once 'lib/couchDocument.php';

class CouchDB {
	
	private $dns 		= NULL;
	private $tableName 	= NULL;
	private $client 	= NULL;
	private $views		= NULL;
	
	/**
	 * @param string $dns 
	 * @param string $tablename, default value = "_users"
	 */
	public function __construct($dns = NULL, $tablename = "_users") {
		if (isset($dns))
			$this->client = new couchClient($dns, $tablename);
	//	$this->views = require_once('../Config/views.php');
 	}
	 
 	public function useDatabase($database) {
 		$this->tableName = $database;
 		if($this->databaseExist($database))
 			$this->client->useDatabase($database);
 		else 
 			$this->createDatabase($database);
 	}
 	
 	public function listDatabases() {
		$databases = NULL;
 		try {
 			$databases = $this->client->listDatabases();
 		} catch ( Exception $e) {
 			echo "Some error happened during the request.\n";
 		}
 		return $databases;
 	}
		
 	public function createDatabase($nameDatabase) {
 		$result = NULL;
 		try {
 			$this->client->useDatabase($nameDatabase);
 			$result = $this->client->createDatabase();
 		} catch (Exception $e) {
 			if ( $e instanceof couchException ) {
 				echo "We issued the request, but couch server returned an error.\n";
 				echo "We can have HTTP Status code returned by couchDB using \$e->getCode() : ". $e->getCode()."\n";
 				echo "We can have error message returned by couchDB using \$e->getMessage() : ". $e->getMessage()."\n";
 				echo "Finally, we can have CouchDB's complete response body using \$e->getBody() : ". print_r($e->getBody(),true)."\n";
 			} else {
 				echo "It seems that something wrong happened. You can have more details using :\n";
 				echo "the exception class with get_class(\$e) : ".get_class($e)."\n";
 				echo "the exception error code with \$e->getCode() : ".$e->getCode()."\n";
 				echo "the exception error message with \$e->getMessage() : ".$e->getMessage()."\n";
 			}
 		}
		return $result;	
 	}
 	
 	public function getDatabaseInformation($nameDatabase){
 		$db_infos = NULL;
 		try {
 			$db_infos = $this->client->getDatabaseInfos();
 		} catch (Exception $e) {
 			echo "Something weird happened  :".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $db_infos;		
 	}
 	
 	public function deleteDatabase($nameDatabase) {
 		$result = NULL;
 		try {
 			$this->client->useDatabase($nameDatabase);
 			$result = $this->client->deleteDatabase();
 		} catch ( Exception $e) {
 			echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $result;
 	}
 	
 	public function databaseExist($name) {
 		$this->client->useDatabase($name);
 		if ( $this->client->databaseExists() )
 			return true;
 		else
 			return false;
 	}
 	
 	public function storeDocument($doc, $id) {
 		$document = new stdClass();
 		$document->_id = $id;
 		$document->content = $doc;
 		try {
 			$response = $this->client->storeDoc($document);
 		} catch (Exception $e) {
 			echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $response;
 	}
 	
 	// return simple document or the object
 	public function getDocument($id) {
 		$doc = NULL;
 		try {
 			$doc = $this->client->getDoc($id);
 		} catch (Exception $e) {
//  			if ( $e->Code() == 404 ) {
//  				echo "Document $id not found\n";
//  			} else {
 				echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
//  			}
 		}
 		return $doc;
 	}
 	
 	public function documentExist($id) {
 		$doc = $this->client->getDoc($id);
 		if(empty($doc))
 			return false;
 		else 
 			return true;
 	}
 	
 	// return all documents
 	public function getAllDocuments() {
 		$all_docs = NULL;
 		try {
 			$all_docs = $this->client->getAllDocs();
 		} catch(Exception $e) {
 			echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $all_docs->rows;
 	}
 	
 	public function getDocumentsFromView($viewName) {
 		return $this->client->getView('all',$viewName);
 	}
 	
 	
 	// $content must be an associative array 
 	// each keys is an element of the document to update
 	// if $content is an object 
 	// the objext will be updated
 	public function updateDocument($id, $toUpdate) {
 		$response = NULL;
 		try {
 			$doc = $this->getDocument($id);
 			$keysToUpdate = array_keys($toUpdate);
			if(!empty($keysToUpdate)) {
				foreach($keysToUpdate as $key) {
					$doc->content->$key = $toUpdate[$key];
					unset($toUpdate[$key]);
				}
			}
 			$response = $this->client->storeDoc($doc);
 		} catch (Exception $e) {
 			echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $response;
 	}
 	
 	public function deleteDocument($id) {
 		$result = NULL;
 		$doc = $this->getDocument($id);
 		try {
 			$result = $this->client->deleteDoc($doc);
 		} catch (Exception $e) {
 			echo "Something weird happened: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
 		}
 		return $result;
 	}
 	
 	public function getDocumentUri($id) {
 		$doc = couchDocument::getInstance($this->client, $id);
 		return  $doc->getUri();
 	}
 	
 	public function addAttachmentAsPath($id, $path) {
	 	$doc = couchDocument::getInstance($this->client, $id);
	 	$infos = pathinfo($path);
	 	try {
			$result = $doc->storeAttachment($path);
		} catch (Exception $e) {
			echo "Error: attachment storage failed : ".$e->getMessage().' ('.$e->getCode().')';
		}
		return $result;
 	}
 	
 	public function addAttachmentAsData($id, $data, $name) {
 		$result = NULL;
 		$doc = couchDocument::getInstance($this->client,$id);
 		try {
 			$result = $doc->storeAsAttachment($data, $name);
 		} catch (Exception $e) {
 			echo "Error: attachment storage failed : ".$e->getMessage().' ('.$e->getCode().')';
 		}
 		return $result;
 	}
 	
 	public function deleteAttachment($id, $documentName) {
 		$result = NULL;
 		$doc = couchDocument::getInstance($this->client,$id);
 		try {
 			$result = $doc->deleteAttachment($documentName);
 		} catch (Exception $e) {
 			echo "Error: attachment removal failed : ".$e->getMessage().' ('.$e->getCode().')';
 		}
 		return $result;
 	}
 	
 	public function getAllAttachmentUri($id) {
 		$result = array();
 		$doc = couchDocument::getInstance($this->client,$id);
 		if ( $doc->_attachments ) {
 			foreach ( $doc->_attachments as $name => $infos ) {
 				$result[$name] = $doc->getAttachmentURI($name);
 			}
 		}
 		return $result;
 	}
 	
 	public function getAttachmentUri($id, $nameAttachment) {
 		$result = array();
 		$doc = couchDocument::getInstance($this->client,$id);
 		if ( $doc->_attachments ) {
 			foreach ( $doc->_attachments as $name => $infos ) {
 				if($name == $nameAttachment)
 					return $doc->getAttachmentURI($name);
 			}
 		}
 		return false;
 	}
 		
 	
 	// create VIEWS 
 	public function createViews() {
 		$design = $this->viewsExist();
 		if(is_array($design)) {
 			foreach ($design as $key => $value) {
 				$this->createView($key, $value, true);
 			}
 		} else if(!$design) {
 			$this->createView();
 		}
 	}
 	
 	private function createView() {
 		$args = func_num_args();
 		if($args === 0 ) {
 			$this->_createViewArray();
 		}else if($args === 2) {
 			$list = func_get_args();
 			$this->_createViewUpdate($list[0], $list[1]);
 		}
 	}
 	
 	private function _createViewArray() {
 		$design_doc = new stdClass();
 		$design_doc->_id = '_design/all';
 		$design_doc->language = 'javascript';
 		$insert = array();
 		foreach ($this->views as $name => $view_fn) {
 			 $insert[$name] = array ('map' => $view_fn) ;
 		}
 		$design_doc->views = $insert;
 		$this->client->storeDoc($design_doc);
 	}
 	
 	private function _createViewUpdate($name, $view_fn) {
 		// update document
 		$doc = $this->getDocument('_design/all');
 		$doc->views = array ( $name => array ('map' => $view_fn ) );
 		$this->client->storeDoc($doc);
 	}
 	
 	private function viewsExist() {
 		$doc = $this->getDocument('_design/all');
 		if(empty($doc)){
 			return false;
 		}
 		else {
 			$this->updateView();
 			if(count(get_object_vars($doc->views)) !== count($this->views)) {
 				$this->deleteDocument('_design/all');
 				$this->_createViewArray();	
 			}
 		}
 		return true;
 	}
 	
 	
	// getters and setters
	
	public function getDns() {
		return $this->dns;
	}
	
	public function setDns($dns) {
		$this->dns = $dns;
	}
	
	public function getTableName() {
		return $this->tableName;
	}
	
	public function setTableName($tableName) {
		$this->tableName = $tableName;		
	}
	
	public function getClient() {
		return $this->client;
	}
	
	public function setClient($client) {
		$this->client = $client;
	}
	
	private function updateView() {
		$this->view = require_once('../Config/views.php');
	}
	
	private function debug($debug) {
		echo "<pre>";
		print_r($debug);
		echo "</pre>";
	}
}
