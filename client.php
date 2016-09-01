ашс<?php

require('phpagi.php');
//require('client.php');
require_once ('/home/katod/projects/PHP_GSM/api/AES128.php');
require_once ('/home/katod/projects/PHP_GSM/api/SHClient.php');


define("REPEAT_TIME",10000);



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
    private $posiblePath = '';
    private $pincode = '';

    public $menu = array();
    private $agi; 
    private $db ;
    private $shClient;

    public function __construct($db_path,$voice_path,$host,$port,$secret_key)
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
          //sleep(5);
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
              $r=$this->agi->get_data($this->pathToVoice."files/ENTER_PIN",REPEAT_TIME,6); //need add to voice 
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
                 $this->agi->stream_file($this->pathToVoice."files/WRONG_PIN"); //need add to voice 
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
             
              if($this->shClient->run2())
              {
                $this->agi->verbose("CONNECT TO SERVER\n",1);
              }
              else 
              {
                $this->agi->verbose("Error connect\n",1);
                $this->agi->stream_file($this->pathToVoice."files/CONNECT_ERROR"); //need add to voice 
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
           $state = $this->shClient->getDeviceStateByAddr($menu_row['addr'],TRUE);
           $this->agi->verbose("type=".$type,1);
            if($menu_row['device_state'] == '')
            {

              $this->shClient->run2();
              sleep(1);
            
              $state = $this->shClient->getDeviceStateByAddr($menu_row['addr'],TRUE);
              $this->agi->verbose("state=" . $state["values"][0]. ";\n".$type,1);
              
              if($state["type"] === '')
              {
                $this->agi->stream_file($this->pathToVoice."files/undefinedType",'*#');
              }
              else
              {
               $state["values"] = explode(".", $state["values"][0]);
                if($state["values"][0] === '')
                {
                   $state["values"][0] = "undefined";
                }
                  $this->agi->stream_file($this->pathToVoice."files/".$state["type"]."/".$state["values"][0],'*#');
              }
            }
            else
            {
              //$this->agi->verbose("Id =".$addr[0]."sub-Id =".$addr[1]." state =".$menu_row['device_state'],1);
              $devices = $this->shClient->setDeviceState((int)$addr[0],(int)$addr[1],["state"=>$menu_row['device_state']]);
              $this->agi->stream_file($this->pathToVoice."files/".$state["type"]."/".$menu_row['device_state'],'*#');
            }
            $this->callPath = substr($this->callPath, 0, -1);
         }
        $this->posiblePath = $this->getMenuPosiblePath($this->callPath).'*#';

        $this->agi->verbose("POSSIBLE =".$this->getMenuPosiblePath($this->callPath),1);
        

        //$digit = $this->agi->get_data($this->pathToVoice.$this->menuID."_".$this->callPath,REPEAT_TIME,1);//'1234567890*#');
      
        $digit = $this->agi->stream_file($this->pathToVoice.$this->menuID."_".$this->callPath ,$this->posiblePath);//$this->getMenuPosiblePath($this->callPath).
       // $digit['result'] = chr($digit['result']);

        
        //$digit = $agi->stream_file("/home/katod/projects/PHP_GSM/Voice/1_".$path,'1234567890*#');
 
        if($digit['result'] <= 0)
        {
           do
           {
            $digit = $this->agi->get_data('beep',REPEAT_TIME,1);
             $this->agi->verbose("DIGIT =".$digit['result']." Code=".$digit['code']." data =".$digit['data'],1);
           }while((strpos($this->posiblePath, $digit['result']) === false) && ($digit['data'] != "timeout")&&($digit['result'] !=''));
        }
        else
        {
        $digit['result'] = chr($digit['result']);
        }
  
        $this->agi->verbose("DIGIT =".$digit['result']." Code=".$digit['code']." data =".$digit['data'],1);

        if($digit['result'] == '*')
          $this->callPath = substr($this->callPath, 0, -1);
        else if(($digit['result'] == '' && $digit['data'] == '')||$digit['result'] =='#')
        {
          $this->agi->verbose("RESET PATHHHHHHHHHHHHHHHHH",1);
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
      $result = '';
      foreach ($this->menu as $key => $value) 
      {
        if (substr($value['path'],0,strlen($path)) == trim($path))
        {
          $sign = trim($value['path'][strlen($path)]);
         // $this->agi->verbose("pass =".strpos($result, $sign),1);
          if(strpos($result, $sign) === false)
            $result .=$sign;
        } 
      }
      return $result; 
     }
}
?>