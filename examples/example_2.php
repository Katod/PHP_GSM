#!/usr/bin/php
<?php
//Пример 2
//получить статус лампы, установить статус на другой, получить новый статус
//0 - лампа выключена, 1 - лампа включена

include_once "settings.php";


$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    $lamp = $shClient->getItemAttrsByType("lamp");

    $lampState = $shClient->getDeviceState($lamp["id"],$lamp["sub-id"]);
    print "lamp state = " . $lampState["state"] . "\n";

    print "set lamp state to " . ((int)$lampState["state"]^1) . "\n";
    $shClient->setDeviceState($lamp["id"], $lamp["sub-id"], array("state"=>((int)$lampState["state"]^1)) );
    sleep(1);

    $sendRequest = true;//сделать запрос на получение статуса лампы
    $lampState = $shClient->getDeviceState($lamp["id"],$lamp["sub-id"],$sendRequest);
    if(array_key_exists("state", $lampState)) print "new lamp state = " . $lampState["state"] . "\n";
    else print "Couldn't get new state\n";

}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>