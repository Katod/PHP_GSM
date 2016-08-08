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
         

         if($ret = $this->db->query("SELECT id FROM clients WHERE phone_number = ".$cid.";\n"))
         //if($ret = $this->db->query("SELECT id FROM clients WHERE phone_number = 12345222;\n"))
         {
            $row = $ret->fetchArray(SQLITE3_ASSOC);

            if ($row != NULL)
            {
              $this->menuID = $row['id'];
              $this->agi->verbose("menuID =".$menuID,1);
            }
            else
            {
              // PIN CODE CHECK 
              $r=$this->agi->get_data('hello-world',14000,6);
              $this->agi->verbose("Result = ".$r['result'],1);

              $ret = $this->db->query("SELECT id FROM clients WHERE PIN = ".$r['result'].";\n");

              $row = $ret->fetchArray(SQLITE3_ASSOC);

              if ($row != NULL)
              {
                $this->menuID = $row['id'];
                $this->agi->verbose("menuID =".$menuID,1);  //CODE DUBLICATE
              }
              else
              {
                 $this->agi->verbose("WRONG PIN",1);
              }
            }
         }

          if($this->menuID != '')
          {
             $ret = $this->db->query("SELECT * FROM gsm_menu WHERE menu_id == ".$this->menuID.";");

             while ($row = $ret->fetchArray(SQLITE3_ASSOC)) 
             {
               if ($row != NULL)
               {
                $this->menu[] = $row;
               }
             }
          }
          else
             $this->agi->verbose("OBJECT DNT CONSTRUCT",1);
        }

      $this->agi->verbose("FINISH Construc ".implode(",", $this->menu),1);
      $this->db->close();
    }

    public function communWithClient()
    {

      $this->agi->verbose("Start Comunication",1);
      do
      {
        $digit = $this->agi->stream_file($this->pathToVoice.$this->menuID."_".$this->callPath,'1234567890*#');
        if($digit['result'] <= 0)
        {
          $digit = $this->agi->wait_for_digit(2000);
          if($digit == 0)
            $this->agi->verbose("DIGIT NULL",1);
        }
        
        $digit = chr($digit['result']);
        if($digit == '*')
          $this->callPath = substr($this->callPath, 0, -1);
        else if($digit == '#')
        {
          $this->callPath = "";
        }
        else 
        {
          $newCallPath =$this->callPath.$digit;

          if($this->checkPath($newCallPath))
            $this->callPath = $newCallPath;
        }
      } while(true);
    }

    public function closeConnectionClient()
    {
      $res=$this->agi->hangup();
    }

    public function checkPath($path)
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