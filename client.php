<?php

require('phpagi.php');
//require('client.php');
require ('api/AES128.php');
require ('api/SHClient.php');


class MyDB extends SQLite3
   {
      function __construct($path)
      {
         $this->open('/home/katod/projects/PHP_GSM/GSM');
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

        $this->db = new MyDB($db_path);
        $this->agi = new AGI();
        $this->pathToVoice = $voice_path;

        $res=$this->agi->answer();
        $cid = $this->agi->request["agi_callerid"];

        $this->agi->verbose($cid."TEST\n",1);



        if(!$this->db)
        {
          $this->agi->verbose( $this->db->lastErrorMsg(),1);
        } 
        else 
        {
          $this->agi->verbose("Opened database successfully\n",1);
         

         //if($ret = $this->db->query("SELECT id FROM clients WHERE phone_number = ".$cid.";\n"))
         if($ret = $this->db->query("SELECT id FROM clients WHERE phone_number = 12345222;\n"))
         {
            $row = $ret->fetchArray(SQLITE3_ASSOC);
            if ($row != NULL)
            {
              $this->menuID = $row['id'];
              $this->agi->verbose("menuID =".$menuID,1);
            }
            else
            {
              $r=$this->agi->get_data('hello-world',14000,6);
              //$this->agi->verbose("Rsult".$r['result'],1);
              $this->agi->verbose("Result = ".$r['result'],1);
            }
         }

             // ENTER PIN CODE
             // $r=$agi->get_data('please-enter-your',14000,9);
             // $account_Number=$r['result'];

           $ret = $this->db->query("SELECT * FROM gsm_menu WHERE menu_id == ".$this->menuID.";");

         //  while ($row = $ret->fetchArray(SQLITE3_ASSOC)) 
         //  {
         //     $this->menu[] = $row;
         //  }
        }


       $this->agi->verbose("FINISH Construc ".implode(",", $this->menu),1);
       $this->db->close();
    }

    public function communWithClient()
    {

    }

    public function closeConnectionClient()
    {
      $res=$this->agi->hangup();
    }

    public function checkPath( $path)
    {
      $isRightPath = false;
      foreach ($this->menu as $key => $value) 
      {
         if (trim($value['path']) == trim($path))
          $isRightPath = true;
      }
      return $isRightPath;
    }


}
?>