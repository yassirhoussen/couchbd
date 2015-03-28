<?php

require_once('../modules/CouchBD/couchBD.class.php');

require_once('../app/Model/Group.model.php');
require_once('../app/Model/User.model.php');

require_once('../app/DAO/DaoFactory.class.php');

$url = "http://82.247.225.181:5984/";

// $arrayGroup = array(
// 	'name'  => 'test',
// 	'right' => 'all'
// );

// $group = new Group();
$user  = new User();

// foreach ($arrayGroup as $key => $value) {
// 	$property = 'set'.ucfirst($key);
// 	$group->$property($value);
// }

$daoFactory = new DaoFactory('group');

$group = $daoFactory->getOne('550df1e31da10');
echo "getOne group\n";
print_r($group);

$arrayUser = array(
	'lastName' 		 => 'Yassir',
	'firstName' 	 => 'Abdullah',
	'adress' 		 => '02 rue de la croix Saint Benoist',
	'furtherAddress' => '',
	'zipCode' 		 => '95130',
	'town' 			 => 'Franconville',
	'email'			 => 'ayassir@iyonn.com',
	'password'		 => '123',
	'groups'		 => $group->content,
);

foreach($arrayUser as $key => $value) {
	$property = 'set'.ucfirst($key);
	$user->$property($value);
}

$daoFactory->setDatabase('user');
$daoFactory->setType('User');

echo "create new Entry in database: User\n\n";
$daoFactory->create($user);

echo "read all Database : User\n\n";
$users = $daoFactory->getAll();
