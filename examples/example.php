#!/usr/bin/php
<?php
require_once "./SHClient.php";

$shClient = new SHClient();
$data = $shClient->getDataFromConsole();


//print_r($data);
//if(count($shClient->errors)) print_r($shClient->errors);

?>