<?php

date_default_timezone_set('Europe/Kiev');

define("BASE_DIR", __DIR__ . "/");
define("XML_FILE", BASE_DIR . "xml/logic.xml");//set chmod 666
define("LOG_FILE", BASE_DIR . "logs/logs.txt");

//begin smart house server settings ##################################
define("HOST", "192.168.1.124");
define("PORT", 55555);
define("SECRET_KEY","0000000000000000");
//define("SECRET_KEY","1234567890123456");
 
/*
define("HOST", "192.168.1.213");
define("PORT", 55555);
define("SECRET_KEY","1111333355557777");
*/
/*
define("HOST", "demo.smarthouse.ua");
define("PORT", 50001);
define("SECRET_KEY","0000000000000000");
*/
//end shs settings ####################################


$globalSettings = array();
$globalSettings["shs"] = array("");
$globalSettings["shs"]["host"] = HOST;
$globalSettings["shs"]["port"] = PORT;
$globalSettings["shs"]["secret_key"] = SECRET_KEY;
$globalSettings["shs"]["logFile"] = LOG_FILE;
$globalSettings["debug"] = TRUE;
$globalSettings["logFile"] = LOG_FILE;


require_once BASE_DIR . 'AES128.php';
require_once BASE_DIR . 'SHClient.php';

?>