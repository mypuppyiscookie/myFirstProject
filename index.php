<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
ob_start(); 
include_once __DIR__ ."getUserInfo.php";

if(isset($_SESSION['userId'])) {
    header("location: /views/main/main.php");
    exit;
} else {
    header("location: /views/user/login.php");
    exit;
}
?>
