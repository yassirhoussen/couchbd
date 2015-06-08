<?php

require_once('../CouchDB.php');

class CouchDBTest extends PHPUnit_Framework_TestCase {
	
	public $dns 	 = "http://192.168.1.19:5984/";
	public $database = 'test_assert';
	public $id 		 = 'sampleid_0123456789';
	public $data 	 = array(
		'sample' => 'id',
		'make'	 => 'things different',
		'couch'  => 'my own client'
		);
	
	
	public function test_list_database() {
		$curl = new CouchDB($this->dns);
		$this->assertContains('test432', $curl->listDatabases());
	}
	
	public function test_create_database() {
		$curl = new CouchDB($this->dns);
		$res  = $curl->createDatabase($this->database);
		$this->assertTrue(true, $res['ok']);
	}
	
	public function test_database_exist() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->databaseExist($this->database));
	}
	
	public function test_database_information() {
		$curl = new CouchDB($this->dns);
		$result = $curl->getDatabaseInformation($this->database);
		$this->assertInternalType('array',$result);
		$this->assertNotEmpty($result);
		$this->assertContains('db_name', $result);
		$this->assertEquals($this->database, $result['db_name']);
	}
	
	public function test_create_document() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res  = $curl->storeDocument($this->data, $this->id); 
		$this->assertTrue(true, $res['ok']);
		for($i = 0; $i < 5; $i++) {
			$res  = $curl->storeDocument($this->data, uniqid()); 
			$this->assertTrue(true, $res['ok']);
		}
	}
	
	public function test_document_exist() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$this->assertTrue(true,$curl->documentExist($this->id));	
	}
	
	public function test_get_all_documents() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->getAllDocuments();
		$this->assertGreaterThan(0, count($res));
		$this->assertInternalType('array',$res);
		$this->assertNotEmpty($res);
	}
	
	public function test_get_document() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->getDocument($this->id);
		$this->assertNotEmpty($res);
		$this->assertEquals($this->id, $res['_id']);
	}
	
	public function test_update_document() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$this->data['update'] = 'update n2';
		$res = $curl->updateDocument($this->id, $this->data); 
		$this->assertNotEmpty($res);
		$this->assertTrue(true, $res['ok']);
		$this->assertEquals($this->id, $res['id']);
	}
	
	public function test_store_attachment_as_data() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$content = 'http://blog.yhabdullah.com/wp-content/uploads/2015/06/Dessin-sans-titre.png';
		$filename = 'image.png';
		$res = $curl->addAttachmentAsData($this->id, $content, $filename);
		$this->assertNotEmpty($res);
		$this->assertTrue(true, $res['ok']);
		$this->assertEquals($this->id, $res['id']);
	}
	
	public function test_store_attachment_as_file() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$content = 'http://blog.yhabdullah.com/wp-content/uploads/2015/06/Dessin-sans-titre.png';
		$filePath = '/tmp/image.png';
		file_put_contents($filePath, file_get_contents($content));
		$res = $curl->addAttachmentAsFile($this->id, $filePath, 'image2.png');
		$this->assertNotEmpty($res);
		$this->assertTrue(true, $res['ok']);
		$this->assertEquals($this->id, $res['id']);
	}
	
	public function test_list_all_attachment() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->listAllAttachment($this->id);	
		$this->assertNotEmpty($res);
	}
	
	public function test_get_all_attachment_uri() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res  = $curl->getAllAttachmentUri($this->id);
		$this->assertNotEmpty($res);
		$res2 = $curl->listAllAttachment($this->id);
		$this->assertEquals(count($res), count($res2));
		$i = 0;
		$j = 0;
		while ($i < count($res) && $j < count($res2)) {
			$this->assertEquals(array_keys($res)[$i], $res2[$j]);
			$i++;$j++;
 		}
	}
	
	public function test_get_attachment_uri() {
		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->getAttachmentUri($this->id, 'image2.png');
		$this->assertContains('image2', $res, '', true);
	}
 	
 	public function test_delete_attachment() {
 		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->deleteAttachment($this->id, 'image2.png');
		$this->assertTrue(true, $res['ok']);
 	}
 	
 	public function test_delete_document() {
 		$curl = new CouchDB($this->dns);
		$this->assertTrue(true, $curl->useDatabase($this->database));
		$res = $curl->deleteDocument($this->id);
		$this->assertTrue(true, $res['ok']);
		$this->assertNotContains($this->id, $curl->getAllDocuments());
 	}
 	
	public function test_delete_database() {
		$curl = new CouchDB($this->dns);
		$res = $curl->deleteDatabase($this->database);
		$this->assertTrue(true, $res['ok']);
		$this->assertNotContains($this->database, $curl->listDatabases());
	}
}

?>