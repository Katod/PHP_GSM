#!/usr/bin/php
<?php
//Пример 4
//получить данные кондиционера
/*
powerSwitch => состояние 
    0 - выключен 
    1 - включен
temperature => заданная температура
    от 16 до 31
air-fan => скорость вентилятора
    0 - Слабый обдув
    1 - Средний обдув
    2 - Сильный обдув
    3 - Максимальный обдув
mode => режим работы
    Ветер - 0
    Холод - 1
    Сушка - 2
    Обогрев - 3
wide_vane => Горизонтальные жалюзи
    1й режим - 0
    2й режим - 1
    3й режим - 2
    4й режим - 3
    5й режим - 4
    6й режим - 5
    7й режим - 6
vane => Вертикальные жалюзи
    1й режим - 0
    2й режим - 1
    3й режим - 2
    4й режим - 3
    5й режим - 4
    6й режим - 5
    7й режим - 6
*/

include_once "settings.php";


$shClient = new SHClient(HOST, PORT, SECRET_KEY, LOG_FILE, XML_FILE);

if($shClient->run()){
    $attrs = $shClient->getItemAttrsByType("conditioner");
    $conditioner = $shClient->getDeviceState($attrs["id"],$attrs["sub-id"]);
    print_r($conditioner);
    print "\n";
}else {
    print implode("\n", $shClient->errors) . "\n";
}

?>