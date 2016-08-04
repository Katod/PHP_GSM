#!/usr/bin/php
<?php
include_once "settings.php";

$readDelay = 100000;//микросекунды
$getSms = true;

$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    if(!$getSms) $shClient->sendMessage2("\x05*111#", 1055, 19);

    while (true) {
        if($shClient->checkConnection()){ 
            $events = $shClient->getDevicesEvents();
            //print_r($events);
            if($getSms){
                if(array_key_exists("1055:35", $events)) {
                    $phone = "";
                    $sms = null;
                    $instr = array();
                    foreach($events["1055:35"] as $v){
                        $instr[] = str_pad(dechex($v), 2, '0', STR_PAD_LEFT);
                        if(dechex($v) != 23 && is_null($sms)){
                            $phone .= chr($v);
                        }elseif(dechex($v) == 23 && is_null($sms)){
                            //$phone .= chr($v);
                            $sms = "";
                        }else $sms .= chr($v);
                    }
                    $sms = iconv("UCS-2", "UTF-8", $sms);
                    echo "phone: ", $phone, "\n", "sms text: ", $sms, "\n";
                    echo implode(" ", $instr), "\n";
                }
            }else {
                if(array_key_exists("1055:19", $events)) {
                    $txt = "";
                    foreach($events["1055:19"] as $v){
                        $txt .= chr($v);
                    }
                    echo $txt, "\n";
                }
            }
        }else {
            sleep(10);
            $shClient->run();
        }
        usleep($readDelay);
    }

}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>