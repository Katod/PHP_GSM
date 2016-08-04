#!/usr/bin/php
<?php
//Пример 6
//получение событий от устройств и датчиков

include_once "settings.php";

$readDelay = 100000;//микросекунды

$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    while (true) {
        if($shClient->checkConnection()){//проверяем связь с сервером 
            //вычитываем события от устройств и датчиков
            //также этот метод вычитывает состояния устройств и датчиков, которые можно получить вызвав метод $shClient->getDevicesState() после вызова метода $shClient->getDevicesEvents()
            $events = $shClient->getDevicesEvents();
            //выводим полученные события
            if(count($events)) print_r($events);
        }else {
            //если соединение с сервером потеряно, пытаемся установить его через 10 секунд
            sleep(10);
            $shClient->run();
        }
        //спим    
        usleep($readDelay);
    }
}else {
    //вывод ошибок
    print implode("\n", $shClient->errors) . "\n";
}

?>