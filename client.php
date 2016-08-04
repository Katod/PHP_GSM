<?php
/**
 * Определение MyClass
 */



class MyDB extends SQLite3
   {
      function __construct()
      {
         $this->open('/home/katod/projects/PHP_GSM/GSM');
      }
   }


class Client
{
    public $pathToVoice = 'Общий';
    protected $menuID = 'Защищенный';
    private $callPath = 'Закрытый';


private:
  std::string pathToVoice;
  std::string phoneNumber;
  int menuID;
  std::string callPath;
  SQLite::Database    db;    //< Database connection
  std::map <std::string,std::string> gsmMenu;


  static int getMen





    public function __construct()
    {
        $this->$foo = TRUE;

        echo($this->$foo);
    }
}
?>