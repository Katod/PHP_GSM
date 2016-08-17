#!/usr/bin/php
<?php


function cache_update($path, $name) 
{
  $name = trim($name);
  $arr_name = explode(".", $name);
  if($arr_name[0] == "on")
    $arr_name[0] = "1";
  else if($arr_name[0] == "off")
    $arr_name[0] = "0";
  
  if($arr_name[1] !== "sln")
  {
  $command="sox $path$name -t raw -r 8000 -s -2 -c 1 $path$arr_name[0].sln";
  echo "Command =".$command;
  exec($command);
  unlink($path.$name);
  }

}



$dir = $argv[1];


if ($handle = opendir($dir)) {
    echo "Дескриптор каталога: $handle\n";
    echo "Файлы:\n";

    /* Именно этот способ чтения элементов каталога является правильным. */
    while (false !== ($file = readdir($handle))) { 
        print_r($file);
        cache_update($dir,$file);
    }

    // /* Этот способ НЕВЕРЕН. */
    // while ($file = readdir($handle)) { 
    //     echo "$file\n";
    // }

    closedir($handle); 
}


?>
