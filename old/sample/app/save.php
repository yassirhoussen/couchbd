<?php

$path = $_SERVER['DOCUMENT_ROOT'].'/';

require($path.'modules/CouchBD/couchBD.class.php');

//DAO
require($path.'app/DAO/IDao.class.php');
require($path.'app/DAO/Dao.class.php');
require($path.'app/DAO/DaoFactory.class.php');

// bl
require($path.'app/BL/UserBL.class.php');

// model
require($path.'app/Model/Group.model.php');
require($path.'/app/Model/User.model.php');

session_start();

$postKeys = array_keys($_POST);

if(in_array('logIn', $postKeys)) {
	if(!empty($_POST['email']) && !empty($_POST['password']) ) {
	    $userBL   = new UserBL();
		$user 	  = $userBL->userExist($_POST['email'], $_POST['password']);
		if(isset($user)) {
			$_SESSION['user'] = $user;
			header("Location: content.php");
			exit(0);
		}
	}
} else if(in_array('SignIn', $postKeys)) {

	$confirmPassword = $_POST['confirmPassword'];
	$passwd 		 = $_POST['password'];
	$user 	= new User();
	if($confirmPassword == $passwd ) {
		foreach($_POST as $key => $value) {
			if($key !== 'confirmPassword' && !empty($value)) {
				$set = 'set'.ucfirst($key);
				$user->$set($value);
			}	
		} //fin foreach
		
		$userBL = new UserBL();
		$result = $userBL->create($user);
		if($result->ok == 1) {
			header("Location: $path.app/Application/index.php");
			exit(0);
		}
	
	}
}
