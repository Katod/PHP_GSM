#!/usr/bin/php
<?php
//codes of city https://pogoda.yandex.ru/static/cities.xml
//<city id="34300" region="20538" head="0" type="3" country="Украина" part="" resort="0" climate="">Харьков</city>
//link for weather http://export.yandex.ru/weather-ng/forecasts/'.$city.'.xml=

//begin smart house server settings ##################################
define("HOST", "192.168.1.124");
define("PORT", 55555);
define("SECRET_KEY","0000000000000000");



require_once 'api/AES128.php';
require_once 'api/SHClient.php';

$debug = TRUE;
//$pCity = 34300;
//$pDev = '135:220';
print_r($argv);
//begin code#################################################################################
$shClient = new SHClient(HOST, PORT, SECRET_KEY);
if($shClient->run()){
	echo "CONNECT SUCCES\n";
	    //$devices = $shClient->getDeviceStateByAddr("765:8", TRUE);
	//$devices = $shClient->getDeviceStateByAddr("765:8");
    //print_r($devices);

}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>