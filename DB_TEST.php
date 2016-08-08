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
  
$db = new MyDB();

if(!$db)
{
  echo $db->lastErrorMsg();
} 

else 
{
  echo "Opened database successfully\n";
}


 $ret = $db->query("SELECT id FROM clients WHERE phone_number = 2134543;\n");

  $row = $ret->fetchArray(SQLITE3_ASSOC);

  if($row == NULL)
      echo "NULL =\n";
  else
      echo "NOT NULL =\n";



 print_r($row);









// $menu = array();
// $ret = $db->query("SELECT * FROM gsm_menu WHERE menu_id == 1 ;\n");

// while ($row = $ret->fetchArray(SQLITE3_ASSOC)) 
// {
//   $menu[] = $row;
// }

// foreach ($menu as $key => $value) {
//   print_r($value['path']);
// }


// $path = "11";

//  $isRightPath = false;

//      foreach ($menu as $key => $value) 
//       {
        
//          if (trim($value['path']) == trim($path))
//          {
//           echo "Test";
//           $isRightPath = true;
//         }
//       }
      
// print_r($isRightPath);








    public function communWithClient()
    {

      $this->agi->verbose("Start Comunication",1);
      $newCallPath = $this->callPath;
      do
      {
        $digit = 0;
        if($newCallPath != $this->callPath)
        {
          $digit = $this->agi->get_data($this->pathToVoice.$this->menuID."_".$newCallPath);//,'1234567890*#');
          $this->callPath = $newCallPath;
        }
        if($digit['result'] <= 0)
        {
          $digit = $this->agi->wait_for_digit("20000");
          if($digit == -1)
            $this->agi->verbose("DIGIT NULL",1);
        }
        
        $digit = chr($digit['result']);
        if($digit == '*')
        {
          $newCallPath = substr($newCallPath, 0, -1);
        }
        else if($digit == '#')
        {
          $newCallPath = "";
        }
        else 
        {
          $checkNewPath =$newCallPath.$digit;

          if($this->checkPath($newCallPath))
            $newCallPath = $checkNewPath;
        }
      } while(true);
    }











//print_r($menu[0]['path']);
$db->close();
?>




