#!/usr/bin/php -q
 <?php

ob_implicit_flush();
error_reporting(0);

require('client.php');

$client =  new Client('/home/katod/projects/PHP_GSM/GSM',"/home/katod/projects/PHP_GSM/Voice/");

$client->communWithClient();

?>