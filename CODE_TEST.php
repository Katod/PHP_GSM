#!/usr/bin/php
<?php

$path="/home/katod/projects/GSM/build/Voice/";


require('client.php');

 function checkPath( $path)
    {
      $isRightPath = false;
      foreach ($this->menu as $key => $value) 
      {
         if (trim($value['path']) == trim($path))
          $isRightPath = true;
      }
      
      return $isRightPath;
    }


$client =  new Client("/home/katod/projects/GSM/GSM","TEST");


echo " ADD CLASS CLIENT\n";


?>
