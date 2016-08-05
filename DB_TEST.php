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



//print_r($menu[0]['path']);
$db->close();
?>
