<?php
require_once('config.php');
require_once(BASE_PATH.'include/DB.php');

$host = "localhost";
$db_user = "root";
$db_password = "";
$niveaesoft_db_name = "tfl";

global $db;
$db = new DB($host,$db_user, $db_password,$niveaesoft_db_name);
?>