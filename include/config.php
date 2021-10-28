<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    ob_start();
    session_start();


    define('BASE_PATH',$_SERVER["DOCUMENT_ROOT"]."/tfl_scripts/php_db/");
    define('ROOT_URL',"/tfl_scripts/php_db");
?>