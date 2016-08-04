#!/usr/bin/php -q
 <?php

ob_implicit_flush();
error_reporting(0);

require('phpagi.php');
require ('api/AES128.php');
require ('api/SHClient.php');


class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('/home/katod/projects/PHP_GSM/GSM');
      }
   }

global $agi;
$agi = new AGI();
$res=$agi->answer();

$cid = $agi->request["agi_callerid"];

$r=$agi->get_data('please-enter-your',14000,9);
$account_Number=$r['result'];

$balance=0;

$username = 'test';
$password = 'tester';

 $agi->verbose("TESSSSSSSSSSSSSSSSSSSSSSSSSSSSST",1);

$db = new MyDB();

if(!$db)
{
  $agi->verbose( $db->lastErrorMsg(),1);
} 
else 
{
  $agi->verbose("Opened database successfully\n",1);
}

$sql =<<<EOF
      SELECT * from gsm_menu;
EOF;
$ret = $db->query($sql);

if($ret == true)
while($row = $ret->fetchArray(SQLITE3_ASSOC))
{
  $agi->verbose($row['path'].$row['file'],1);
}



$path = "";
 // $agi->stream_file("/home/katod/projects/PHP_GSM/Voice/1_".$path);
do
{
  $digit = $agi->wait_for_digit(20000);
  $digit =  chr($digit['result']);
  $agi->verbose("DEGIT =".chr($digit['result']),1);
  
  if($digit == '*')
    $path = substr($path, 0, -1);
  else if($digit == '#')
    $path = "";
  else
    $path .= $digit;
 //  $agi->evaluate("S,/home/katod/projects/PHP_GSM/Voice/1_)");
    fwrite(STDOUT, trim("STREAM FILE home/katod/projects/PHP_GSM/Voice/1_\"\" 0")."\n");
    fflush($stdout);
  //$agi->stream_file("/home/katod/projects/PHP_GSM/Voice/1_".$path);
  // $result = $agi->get_data('beep', 3000, 20);
  // $keys = $result['result'];

} while(true);



$agi->hangup();
$db->close();

?>