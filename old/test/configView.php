<?php
// create all the views at the start of application
// $views is an associative array with contain :
//  	the key is the name of the couchBD view
//		the value is the couchBD view 

$views = array(
		'all' 		=> 'function(doc) { emit(doc._id,doc); }',	
		'content'  	=> 'function(doc) { emit(doc._id, doc.content); }',
);

return $views;