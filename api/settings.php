<?php

define("BASE_DIR", "/home/");
define("XML_FILE", BASE_DIR . "sh2/logic.xml");//set chmod 666
define("LOG_FILE", BASE_DIR . "sh2/logs/logs.txt");

//begin smart house server settings ##################################
define("HOST", "192.168.1.125");
define("PORT", 55555);
define("SECRET_KEY","1234567890123456");
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

require ('api/AES128.php');
require ('api/SHClient.php');

?>