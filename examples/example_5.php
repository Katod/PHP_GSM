#!/usr/bin/php
<?php
//Пример 5
//получить значения датчиков и устройств которые можно показывать(выбранных методом $this->getDisplayedDevices())
include_once "settings.php";


$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    $devices = $shClient->getDevicesState();
    print_r($devices);
    print "\n";
}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>