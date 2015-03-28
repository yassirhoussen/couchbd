<?php

$path = $_SERVER['DOCUMENT_ROOT'].'/';
require_once($path.'app/save.php');

if(isset($_SESSION['user'])) {
	echo "<pre>";
	echo "SUCCESS\n";
	print_r($_SESSION['user']);
	echo "<pre>";
}
else 
	header("Location: $path/app/Application/index.php");
	exit(0);
?>