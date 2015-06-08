<?php

require_once('ICouchDB.php');
require_once('Client.php');

class CouchDB implements ICouchDB {
	
	private $dns 	  = null;
	private $database = null;
	private $client   = null;
	private $result	  = null;
	
	public function __construct($dns, $database = null) {
		if (isset($database) && !is_null($database))
			$this->database = $database;
		if(isset($dns) && !empty($dns))
			$this->dns 	= $dns;
		else 
			throw new Exception('CouchDB Error : $dns must be specified');
		$this->client 	= new Client($this->dns);
	}
	
		/**
	 * The database to use to request in CouchDB server
	 * @params $nameDatabase : The database to create
	 * @return $this;
	 */
	public function useDatabase($nameDatabase) {
		if ($this->databaseExist($nameDatabase)) {
			$this->database = $nameDatabase;
			return true;
		} else 
			throw new Exception("CouchDB Error : The database ".$nameDatabase." to use not exist.");
	}
	
	/**
	 * Return the list of all databases present in CouchDB server
	 * @return array
	 */
	public function listDatabases() {
		$res = $this->client->parseResponse($this->client->query('GET', '_all_dbs'));
		return $res['body'];
	}
	
	/**
	 * This method let your create a database in CouchDB server
	 * @params $nameDatabase : The database to create
	 * @return current object $this
	 */
	public function createDatabase($nameDatabase) {
		if (!empty($nameDatabase)) {
			$res = $this->client->parseResponse($this->client->query('PUT', urlencode($nameDatabase)));
			return $res['body'];
		}
		else 
			throw new Exception("CouchDB Error : Unable to create database : ".$nameDatabase.".");
	}
	
	/**
	 * This method return all the information about a database
	 * @params $nameDatabase :  The database name 
	 * @return array
	 */ 
	public function getDatabaseInformation($nameDatabase) {
		if (!empty($nameDatabase)) {
			$res = $this->client->parseResponse($this->client->query('GET', urlencode($nameDatabase)));
			return $res['body'];
		}
		else 
			throw new Exception("CouchDB Error : Unable to retrieve database : ".$nameDatabase." information.");
	}
	
	/**
	 * This method check if a specific with a specific a name exist
	 * @params $nameDatabase : The database name 
	 * @return boolean
	 */
	public function databaseExist($nameDatabase) {
		$res = $this->listDatabases();
		if(in_array($nameDatabase, $res))
			return true;
		else 
			return false;
	}
	
	/**
	 * This method let you delete a specific database in CouchDB server
	 * @params $nameDatabase :  The database name 
	 * @return array
	 */ 
	public function deleteDatabase($nameDatabase) {
		if (!empty($nameDatabase)) {
			$res =  $this->client->parseResponse($this->client->query('DELETE', urlencode($nameDatabase)));
			return $res['body'];
		}
		else 
			throw new Exception("CouchDB Error : Unable to delete database : ".$nameDatabase.".");
	}
	
	/**
	 * This method let you store a specific Document in a database, We assume that the database we used
	 * is the one specified in
	 * @params $document array : the document you want to store
	 * @params $id : the document id, if null the method will generate one
	 * @return array which is the statement of the execution
	 */
	public function storeDocument(&$document, $id = null) {
		$content = null;
		if (is_null($id))
			$id = uniqid();
		if (!is_array($document))
			throw new Exception('CouchDB Error : the first parameter must be an array');
		$res =  $this->client->parseResponse(
						$this->client->query(
							'PUT',
							urlencode($this->database).'/'.$id,
							array(200),
							json_encode($document)
						)
					);
		return $res['body'];
	}
	
	/**
	 * This method let you get a Document from a specific Database
	 * @params $id : the document id
	 * @return array with all values 
	 */
	public function getDocument($id) {
		$res =  $this->client->parseResponse(
						$this->client->query(
							'GET',
							urlencode($this->database).'/'.$id
						)
					);
		return $res['body'];
	}
	
	/**
	 * This method check if a document exist in a specific Database
	 * @params $nameDatabase : the database name where we store the document
	 * @params $id : the document id
	 * @return boolean value
	 */ 
	public function documentExist($id) {
		$res =  $this->client->parseResponse(
				$this->client->query(
					'GET',
					urlencode($this->database).'/'.$id
				)
			);
		if ($res['status_code'] == 200)
			return true;
		else 
			return false;
	}

	/**
	 * This method make you update a specific document with his id 
	 * @params $id : the document id
	 * @params $toUpdate array the document
	 * @return array which is the statement of the execution
	 */
	public function updateDocument($id, &$toUpdate) {
		//the document rev to update
		$res =  $this->client->parseResponse(
				$this->client->query(
					'GET',
					urlencode($this->database).'/'.$id
				)
			);
		$rev = $res['body']['_rev'];
		$result = $this->client->parseResponse(
				$this->client->query(
					'PUT',
					urlencode($this->database).'/'.$id.'?rev='.$rev,
					array(),
					json_encode($toUpdate)
				)
			);
		return $result['body'];
	}
	
	/**
	 * This method let you delete a specific document with his id
	 * @params $id : the document id
	 * @return array which is the statement of the execution
	 */ 
	public function deleteDocument($id) {
		//the document rev to delete
		$res =  $this->client->parseResponse(
				$this->client->query(
					'GET',
					urlencode($this->database).'/'.$id
				)
			);
		$rev = $res['body']['_rev'];
		$result = $this->client->parseResponse(
				$this->client->query(
					'DELETE',
					urlencode($this->database).'/'.$id.'?rev='.$rev
				)
			);
		return $result['body'];
	}
	
	/**
	 * This method return an array of all the documents in a specific database
	 * @return  an array of all documents found in the database
	 */ 
	public function getAllDocuments() {
		$res = $this->client->parseResponse(
			$this->client->query(
				'GET',
				urlencode($this->database).'/_all_docs'
			)
		);
		$result = array();
		foreach($res['body']['rows'] as $row) {
			$result [] = $this->getDocument($row['id']); 
		}
		return $result;
	}
	
	/**
	 * This method will add an attachment to a couch Document
	 * @params $id : the document id
	 * @params $filePath : the attachment path from root '/'
	 * @params $nameAttachment : $the document name
	 * @return array which is the statement of the execution
	 */
	public function addAttachmentAsFile($id, $filePath, $nameAttachment) {
		$res =  $this->client->parseResponse(
			$this->client->query(
				'GET',
				urlencode($this->database).'/'.$id
			)
		);
		$rev = $res['body']['_rev'];
		$_url  = urlencode($this->database).'/'.urlencode($id).'/'.urlencode($nameAttachment);
		$_url .= '?rev='.$rev;
		$raw = $this->client->parseResponse($this->client->storeFile($_url, $filePath));
		return $raw['body'];
	}
	
	/**
	 * This method will add an attachment to a couch Document
	 * @params $id : the document id
	 * @params $data : the attachment as a link
	 * @params $nameAttachment : $the document name
	 * @return array which is the statement of the execution
	 */
	public function addAttachmentAsData($id, &$data, $nameAttachment) {
		$res =  $this->client->parseResponse(
			$this->client->query(
				'GET',
				urlencode($this->database).'/'.$id
			)
		);
		$rev = $res['body']['_rev'];
		
		$_url  = urlencode($this->database).'/'.urlencode($id).'/'.urlencode($nameAttachment);
		$_url .= '?rev='.$rev;
		$raw = $this->client->parseResponse($this->client->storeAsData($_url, file_get_contents($data)));
		return $raw['body'];
	}
	
	/**
	 * This method will list all attachment in a document
	 * @params: $id : the document Id
	 * @return array : list of all attachments' URIs
	 */
	public function listAllAttachment($id) {
		$doc = $this->client->parseResponse($this->client->query('GET', urlencode($this->database).'/'.$id));
		return $attachment = array_keys($doc['body']['_attachments']);
	}
	
	/**
	 * This method will delete an attachment to a specific document 
	 * @params: $id : the document Id
	 * @params: $nameAttachment : the attachment's name to delete
	 * @return array which is the statement of the execution
	 */ 
	public function deleteAttachment( $id, $nameAttachment) {
		$doc   = $this->client->parseResponse($this->client->query('GET', urlencode($this->database).'/'.$id));
		$_rev  = $doc['body']['_rev'];
		$_url  = urlencode($this->database).'/'.$id.'/'.urlencode($nameAttachment).'?rev='.$_rev;
		$res   = $this->client->parseResponse( $this->client->query('DELETE', $_url) );
		return $res['body'];
	}
	
	/**
	 * This method will return the URLs from attachment in a specific document
	 * eg : http://localhost:5984/sampleDatabase/123/toto.jpg
	 * @params: $id : the document Id
	 * @return array : list of all attachments' URIs
	 */
	public function getAllAttachmentUri($id) {
		$list = array();
		$attachment = $this->listAllAttachment($id);
		foreach($attachment as $a) {
			$list[$a] = $this->dns.urlencode($this->database).'/'.$id.'/'.$a;
		}
		return($list);
	
	}
	
	/**
	 * This method will return the URL from a specfifc attachment by his name in a specific document
	 * eg : http://localhost:5984/sampleDatabase/123/toto.jpg
	 * @params: $id : the document Id
	 * @params: $nameAttachment : the attachment's name
	 * @return array : containing the attachment's URI
	 */
	public function getAttachmentUri($id, $nameAttachment) {
		$list = $this->getAllAttachmentUri($id);
		if(in_array($nameAttachment, array_keys($list)))
			return $list[$nameAttachment];
		else 
			return false;
	}
	
}