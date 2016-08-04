#!/usr/bin/php
<?php

$path="/home/katod/projects/GSM/build/Voice/";

class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('GSM');
      }
   }
  
function cache_update($name,$text) 
{
  $name = trim($name);
  echo "Generating file " . $text . "\n";
  echo "NAME = ".$name."\n";
  file_put_contents("temp.wav", file_get_contents("http://s2.smarthouse.ua:8080/say_wav.php?text=".urlencode($text)));
  $command="sox temp.wav -t raw -r 8000 -s -2 -c 1 Voice/".$name.".sln";
 
  echo "Command =".$command;
  exec($command);
}
  
$db = new MyDB();

if(!$db)
{
  echo $db->lastErrorMsg();
} 
else 
{
  echo "Opened database successfully\n";
}

$sql =<<<EOF
      SELECT * from gsm_menu;
EOF;
$ret = $db->query($sql);


while($row = $ret->fetchArray(SQLITE3_ASSOC) )
{
  cache_update($row['menu_id']."_".$row['path'],$row['file']);
}
echo "Operation done successfully\n";
$db->close();
?>
