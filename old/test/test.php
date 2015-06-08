<?php
require_once('../modules/CouchBD/couchBD.class.php');
$database = 'test';
$url 	  = 'http://127.0.34:5984/';
$couch = new CouchDB($url, $_SERVER['DOCUMENT_ROOT'].'/couchbd/test/configView.php');

echo "list all database";
echo "<pre>";
print_r($couch->listDatabases());
echo "</pre>";

echo "create database";
echo "<pre>";
print_r($couch->createDatabase($database));
echo "</pre>";

echo "get information database";
echo "<pre>";
print_r($couch-> getDatabaseInformation($database));
echo "</pre>";

echo "use database: $database <br/>";
$couch->useDatabase($database);


echo "create Document";
$id  = uniqid();
$doc = array('id' => $id, 'content' => 'simple dummy content');
echo "<pre>";
print_r($couch->storeDocument($doc, $id));
echo "</pre>";

echo "readAllDocument";
echo "<pre>";
print_r($couch->getAllDocuments());
echo "</pre>";


echo "read Document $id";
echo "<pre>";
$couch->useDatabase($database);
$doc = $couch->getDocument($id);
print_r($doc);
echo "</pre>";

echo "update Content";
echo "<pre>";
$toUpdate = array("content" => 'simple dummmy update', "value" => "new data entry");
$result = $couch->updateDocument($id, $toUpdate);
print_r($result);
print_r($couch->getDocument($id));
echo "</pre>";


echo "add list of Attachments as Path";
echo "<pre>";
$folder = 'attachment/';
$files  = array(); 
if ($handle = opendir($folder)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $files[] = $folder.$entry;
        }
    }
    closedir($handle);
}
$dir = dirname(__FILE__);

foreach($files as $file) {
	echo $dir.'/'.$file."\n";
	print_r($couch->addAttachmentAsPath($id, $dir.'/'.$file)); 
}

echo "</pre>";

echo "add list of Attachments as Data";
echo "<pre>";
$data = array('intimicy.jpg' => 'http://40.media.tumblr.com/643ab1527b07704dad4764704c6b4608/tumblr_nlem1pVmZt1qzy9ouo1_1280.jpg', 
			  'auLit.jpg' => 'http://40.media.tumblr.com/f11cd410fbaf3aab9619997bbe16273e/tumblr_nl56zlBxtA1qzy9ouo1_1280.jpg',
			  'ginger.jpg' => 'http://40.media.tumblr.com/7159895e50226941aec30b865d0be384/tumblr_nkbrwnOvgu1qzy9ouo1_1280.jpg'
			 );
foreach($data as $name => $image) {
	$d = file_get_contents($image);
	print_r($couch->addAttachmentAsData($id, $d, $name));
}
echo "</pre>";

echo "get one Attachment";
echo "<pre>";
	print_r($couch->getAttachmentUri($id, 'ginger.jpg'));
echo "</pre>";


echo "get all attachment URI";
echo "<pre>";
print_r($couch->getAllAttachmentUri($id));
echo "</pre>";

echo "create views";
echo "<pre>";
print_r($couch->createViews());
echo "</pre>";

echo "query view : all";
echo "<pre>";
print_r($couch->query('all'));
echo "</pre>";

echo "delete Document";
echo "<pre>";
print_r($couch->deleteDocument($id));
echo "</pre>";

echo "delete Database";
echo "<pre>";
print_r($couch->deleteDatabase($database));
echo "</pre>";

?>