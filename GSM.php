#!/usr/bin/php -q
 <?php

ob_implicit_flush();
error_reporting(0);

require('client.php');
define("HOST", "192.168.1.124");
define("PORT", 55555);
define("SECRET_KEY","0000000000000000");


$client =  new Client('/home/katod/projects/PHP_GSM/GSM',"/home/katod/projects/PHP_GSM/Voice/",HOST,PORT,SECRET_KEY);

$client->communWithClient();

?>