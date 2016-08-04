#!/usr/bin/php
<?php
//Пример 3
//получить статус и значение яркости димерной лампы, установить другой статус и значение яркости, получить новый статуc и значение яркости
//0 - лампа выключена, 1 - лампа включена, диапозон значения яркости от 0 до 100

include_once "settings.php";


$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    $dimerLamp = $shClient->getItemAttrsByType("dimer-lamp");

    $dimerLampState = $shClient->getDeviceState($dimerLamp["id"],$dimerLamp["sub-id"]);
    print "dimer lamp state=" . $dimerLampState["state"] . "; value=" . $dimerLampState["value"] . ";\n";

    $newState = rand(0, 1);
    $newValue = rand(0, 100);
    print "set dimer lamp state to " . $newState . " and set value to " . $newValue . "\n";
    $shClient->setDeviceState($dimerLamp["id"], $dimerLamp["sub-id"], array("state"=>$newState, "value"=>$newValue) );

    sleep(2);

    $sendRequest = true;//сделать запрос на получение статуса димерной лампы
    $dimerLampState = $shClient->getDeviceState($dimerLamp["id"],$dimerLamp["sub-id"],$sendRequest);
    if(array_key_exists("state", $dimerLampState)) print "dimer lamp new state=" . $dimerLampState["state"] . "; new value=" . $dimerLampState["value"] . ";\n";
    else print "Couldn't get new state and value\n";

}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>