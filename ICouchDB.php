<?php

interface ICouchDB {
	
	/**
	 * The database to use to request in CouchDB server
	 * @params $nameDatabase : The database to create
	 * @return $this;
	 */
	public function useDatabase($nameDatabase);
	
	/**
	 * Return the list of all databases present in CouchDB server
	 * @return array
	 */
	public function listDatabases();
	
	/**
	 * This method let your create a database in CouchDB server
	 * @params $nameDatabase : The database to create
	 * @return current object $this
	 */
	public function createDatabase($nameDatabase);
	
	/**
	 * This method return all the information about a database
	 * @params $nameDatabase :  The database name 
	 * @return array
	 */ 
	public function getDatabaseInformation($nameDatabase);
	
	/**
	 * This method let you delete a specific database in CouchDB server
	 * @params $nameDatabase :  The database name 
	 * @return array
	 */ 
	public function deleteDatabase($nameDatabase);
	
	/**
	 * This method check if a specific with a specific a name exist
	 * @params $nameDatabase : The database name 
	 * @return boolean
	 */
	public function databaseExist($nameDatabase) ;
	
	/**
	 * This method let you store a specific Document in a database
	 * @params $document : the document you want to store
	 * @params $nameDatabase : the database name where we store the document
	 * @params $id : the document id
	 * @return array which is the statement of the execution
	 */
	public function storeDocument(&$document, $id);
	
	/**
	 * This method let you get a Document from a specific Database
	 * @params $id : the document id
	 * @return Object return the document 
	 */
	public function getDocument($id);
	
	/**
	 * This method check if a document exist in a specific Database
	 * @params $id : the document id
	 * @return boolean value
	 */ 
	public function documentExist($id);

	/**
	 * This method make you update a specific document with his id 
	 * @params $id : the document id
	 * @params $toUpdate : the document
	 * @return array which is the statement of the execution
	 */
	public function updateDocument($id, &$toUpdate);
	
	/**
	 * This method let you delete a specific document with his id
	 * @params $id : the document id
	 * @return array which is the statement of the execution
	 */ 
	public function deleteDocument($id);
	
	/**
	 * This method return an array of all the documents in a specific database
	 * @return  an array of all documents found in the database
	 */ 
	public function getAllDocuments();
	
	/**
	 * This method will add an attachment to a couch Document
	 * @params $id : the document id
	 * @params $filePath : the attachment path from root '/'
	 * @params $nameAttachment : $the document name
	 * @return array which is the statement of the execution
	 */
	public function addAttachmentAsFile($id, $filePath, $nameAttachment);
	
	/**
	 * This method will add an attachment to a couch Document
	 * @params $id : the document id
	 * @params $data : the attachment as a link
	 * @params $nameAttachment : $the document name
	 * @return array which is the statement of the execution
	 */
	public function addAttachmentAsData($id, &$data, $nameAttachment);
	
	/**
	 * This method will list all attachment in a document
	 * @params: $id : the document Id
	 * @return array : list of all attachments' URIs
	 */
	public function listAllAttachment($id);
	
	/**
	 * This method will delete an attachment to a specific document 
	 * @params: $id : the document Id
	 * @params: $nameAttachment : the attachment's name to delete
	 * @return array which is the statement of the execution
	 */ 
	public function deleteAttachment($id, $nameAttachment);
	
	/**
	 * This method will return the URLs from attachment in a specific document
	 * eg : http://localhost:5984/sampleDatabase/123/toto.jpg
	 * @params: $id : the document Id
	 * @return array : list of all attachments' URIs
	 */
	public function getAllAttachmentUri($id);
	
	/**
	 * This method will return the URL from a specfifc attachment by his name in a specific document
	 * eg : http://localhost:5984/sampleDatabase/123/toto.jpg
	 * @params: $id : the document Id
	 * @params: $nameAttachment : the attachment's name
	 * @return array : containing the attachment's URI
	 */
	public function getAttachmentUri( $id, $nameAttachment);
	
}