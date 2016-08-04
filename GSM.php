#!/usr/bin/php -q
 <?php

ob_implicit_flush();
error_reporting(0);

require('phpagi.php');
//require('client.php');
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

do
{
  $digit = $agi->stream_file("/home/katod/projects/PHP_GSM/Voice/1_".$path,'1234567890*#');

  if($digit['result'] <= 0)
  {
    $digit = $agi->wait_for_digit(20000);
  }
  
  $digit = chr($digit['result']);

  if($digit == '*')
    $path = substr($path, 0, -1);
  else if($digit == '#')
  {
    $path = "";
  }
  else 
  {
    $path .= $digit;
  }
} while(true);

$agi->hangup();
$db->close();

?>