#!/usr/bin/php
<?php
//Пример 1
//получить значение датчика температуры

include_once "settings.php";


$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    $temperatureSensor = $shClient->getItemAttrsByType("temperature-sensor");
    $temperature = $shClient->getDeviceState($temperatureSensor["id"],$temperatureSensor["sub-id"]);
    //$temperature = $shClient->getDeviceState(11,8);
	print "temperature = " . $temperature["state"] . "\n";
}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>