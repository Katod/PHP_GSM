#!/usr/bin/php -q
 <?php

ob_implicit_flush();
error_reporting(0);

require('client.php');

$client =  new Client("GSM","TEST");

?>