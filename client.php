ашс<?php

require('phpagi.php');
//require('client.php');
require_once ('/home/katod/projects/PHP_GSM/api/AES128.php');
require_once ('/home/katod/projects/PHP_GSM/api/SHClient.php');

define("HOST", "192.168.1.124");
define("PORT", 55555);
define("SECRET_KEY","0000000000000000");
define("REPEAT_TIME",50000);



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
    private $shClient;

    public function __construct($db_path,$voice_path)
    {

        $this->db = new MyDB($db_path);
        $this->agi = new AGI();
        $this->pathToVoice = $voice_path;

        $res=$this->agi->answer();
        $cid = $this->agi->request["agi_callerid"];

        $this->agi->verbose($cid."TEST\n",1);
        $this->agi->verbose($_SERVER['argv'][1]." = ARG\n",1);
      
       // $this->agi->verbose($_SERVER['argv']."ARGV\n",1);
        // SHClient Create


        if(!$this->db)
        {
          $this->agi->verbose( $this->db->lastErrorMsg(),1);
        } 
        else 
        {
          $this->agi->verbose("Opened database successfully\n",1);
        

        if($_SERVER['argv'][1] != NULL)
        {
          $this->menuID = $_SERVER['argv'][1];
        }
        else
        {
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
              $r=$this->agi->get_data('hello-world',14000,6); // Add voice enter pin 
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
      
             $this->shClient = new SHClient(HOST, PORT, SECRET_KEY);
             
              if($this->shClient->run())
              {
                $this->agi->verbose("CONNECT TO SERVER\n",1);
              }
              else 
              {
                $this->agi->verbose("Error connect\n",1);
                $this->agi->stream_file($this->pathToVoice."files/".$type."/"."CONNECT_ERROR");
                //$this->agi->hangup();
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
      $oldPath = "start";
      $this->agi->verbose("Start Comunication",1);
      do
      {
        //if($oldPath != $this->callPath)
        //{
        $menu_row = $this->getMenuByPath($this->callPath);
        
         if($menu_row['addr'] != '')
         {
           $addr = explode(":", $menu_row['addr']);
           $type = $this->shClient->getItemType($addr[0], $addr[1]);
            if($menu_row['device_state'] == '')
            {
              $state = $this->shClient->getDeviceState($addr[0], $addr[1],TRUE);
              //$state = $this->shClient->getDeviceState($addr[0], $addr[1],TRUE);

              $this->agi->verbose("state=" . $state["state"] . ";\n".$type,1);
            
             // if($state["state"] != null)
             // {
             //    $state["state"] = "undefined";
             //  }
            $this->agi->stream_file($this->pathToVoice."files/".$type."/".$state['state'],'*#');
            }
            else
            {
              //$this->agi->verbose("Id =".$addr[0]."sub-Id =".$addr[1]." state =".$menu_row['device_state'],1);
              $devices = $this->shClient->setDeviceState((int)$addr[0],(int)$addr[1],["state"=>$menu_row['device_state']]);
              $this->agi->stream_file($this->pathToVoice."files/".$type."/".$menu_row['device_state'],'*#');
            }

            $this->callPath = substr($this->callPath, 0, -1);
         }
        
        $digit = $this->agi->get_data($this->pathToVoice.$this->menuID."_".$this->callPath,REPEAT_TIME,1);//'1234567890*#');

        // $oldPath = $this->callPath;
        //}
        // else
        //{
        //  $digit = $this->agi->get_data(test,20000,1);
        //}

        $this->agi->verbose("DIGIT =".$digit['result']." Code=".$digit['code']." data =".$digit['data'],1);
        if($digit['result'] == '*')
          $this->callPath = substr($this->callPath, 0, -1);
        else if($digit['result'] == '' && $digit['data'] == '')
        {
          $this->callPath = "";
        }
        else 
        {
          $newCallPath =$this->callPath.$digit['result'];

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
      foreach ($this->menu as $key => $value) 
      {
         if (trim($value['path']) == trim($path))
          return true;
      }
      return false;
    }

    public function getMenuByPath($path)
    {
      foreach ($this->menu as $key => $value) 
      {
        if (trim($value['path']) == trim($path))
          return $value;      
      }
      return NULL;
    }

     public function getMenuPosiblePath($path)
     {
      $result = array();
      foreach ($this->menu as $key => $value) 
      {
        if (trim($value['path']) == trim($path))
          return $value;      
      }
      return NULL; 
     }
}
?>