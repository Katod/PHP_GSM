<?php

require('phpagi.php');
//require('client.php');
require ('api/AES128.php');
require ('api/SHClient.php');


class MyDB extends SQLite3
   {
      function __construct($path)
      {
         $this->open($path);
      }
   }

class Client
{
    private $pathToVoice = '';
    private $menuID = '';
    private $callPath = '';
    private $pincode = '';

    public $menu = array();
    private $agi; 
    private $db ;


    public function __construct($db_path,$voice_path)
    {
        echo "TEST";

        $this->db = new MyDB($db_path);
        $this->agi = new AGI();
        $this->pathToVoice = $voice_path;

        $res=$this->agi->answer();
        
        //$this->agi->wait_for_digit();
        echo "TEST";

        if(!$this->db)
        {
          $this->agi->verbose( $this->db->lastErrorMsg(),1);
        } 
        else 
        {
          $this->agi->verbose("Opened database successfully\n",1);
         

         if($ret = $this->db->query("SELECT id FROM clients WHERE phone_number = 777;"))
         {

          if ($row = $ret->fetchArray(SQLITE3_ASSOC)) 
          {
            $this->menuID = $row['id'];

        //    global $agi;
        //    $this->agi = new AGI();
       //     $res=$this->agi->answer();

      //      $cid = $this->agi->request["agi_callerid"];

           // $r=$this->agi->get_data('please-enter-your',14000,9);
           // $account_Number=$r['result'];

           // $balance=0;

           // $username = 'test';
           // $password = 'tester';

             $this->agi->verbose("TESSSSSSSSSSSSSSSSSSSSSSSSSSSSST",1);


         }
       }
             // else
             // ENTER PIN CODE
             // $r=$agi->get_data('please-enter-your',14000,9);
             // $account_Number=$r['result'];


          $ret = $this->db->query("SELECT * FROM gsm_menu WHERE menu_id == ".$this->menuID.";");

          while ($row = $ret->fetchArray(SQLITE3_ASSOC)) 
           {
             $this->menu[] = $row;
           }
          
        }

       $this->db->close();
    }
}
?>