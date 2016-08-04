#!/usr/bin/php
<?php
date_default_timezone_set('Europe/Kiev');

define("HOST", "192.168.1.213");
define("PORT", 55555);

require_once 'classes/AES128.php';
require_once 'classes/SHClient.php';

$timeout = 10;//seconds
$readDelay = 100000;//microseconds
$debug = TRUE;

//begin code#################################################################################
$shClient = new SHClient(HOST, PORT);

if($shClient->run()){
    $consoleParams = $shClient->getDataFromConsole();
    $pNumber = $consoleParams["params"]["number"];
    $pDev = trim($consoleParams["params"]["dev"]);
    if($debug) echo "In params: \n", implode("\n", $consoleParams["params"]), "\n";
    $dstId = null;
    $dstSubId = null;
    if(strpos($pDev, ":") !== FALSE) list($dstId, $dstSubId) = explode(":", $pDev);
    if(is_null($dstId) || is_null($dstSubId)){
        echo "destination address not found\n";
        exit;
    }
    $uusdCommand = "\x05" . $pNumber;
    if($debug) echo "Send uusd command\n", $uusdCommand, "\n";
    $shClient->sendMessage2($uusdCommand, 1055, 19);
    if($debug) echo "receiving event\n";
    $startTime = time();
    while ((time()-$timeout) < $startTime) {
        if($shClient->checkConnection()){ 
            $events = $shClient->getDevicesEvents();
            if(array_key_exists("1055:19", $events)) {
                $eventData = "";
                foreach($events["1055:19"] as $k=>$v) if($k > 0) $eventData .= chr($v);

                if($debug) echo "received event\n", $eventData, "\n\n", "set status by addr ", $dstId, ":", $dstSubId, "\n";
                $shClient->sendMessage2($eventData, (int)$dstId, (int)$dstSubId);
                if($debug) echo "success!\n";
                break;
            }
        }else {
            sleep(2);
            $shClient->run();
        }
        usleep($readDelay);
    }
    if($debug) echo "finished\n";
}else {
    if($debug) echo implode("\n", $shClient->errors) , "\n";
}

?>