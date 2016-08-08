#!/usr/bin/php5
<?php
//Обновлено
//Пример 1
//получение статусов и событий от устройств и датчиков

include_once "settings.php";

$globalSettings["listenEventsDelay"] = 70000;//микросекунды 
/*
(это пауза, чтобы процессор не грузить в вечном цикле, 
если не все события приходят, 
то можно поставить паузу в ноль, и посмотреть как процессор грузится, 
если немного съедает ресурса процессора, то можно оставить в нуле)
*/

ini_set("max_execution_time", 0);
ini_set("max_input_time", 0);
set_time_limit(0);
		

$controller = new ShEvents($globalSettings);
$controller->run();


class ShEvents {

	public $error = FALSE;
	public $shClient = NULL;
	public $debug = FALSE;
	public $settings = array();
	public $logFile = "";
	public $devicesStore = array();
	public $allowedType = array("air-fan", 
								"lamp", 
								"dimer-lamp", 
								"rgb-lamp", 
								"jalousie", 
								"gate", 
								"light-scheme", 
								"conditioner", 
								"script");

	public function __construct($settings) {

		$this->settings = $settings;
		$this->logFile = $this->settings["logFile"];
		$this->debug = $this->settings["debug"];
		
		
	}
	
	public function __destruct(){
		$this->debug(__METHOD__);
	}
	
	public function run($connection = 1) {
		if($this->error) return FALSE;
		$this->debug(__METHOD__);
		
		$this->debug("connection #: " . $connection); 
		$this->connectToShS();

		if(!$this->error){
			$methodName = "onGetEvent";
			$callback = array($this, $methodName);
			$this->shClient->sendCommandToSH("get-events", $callback);

			$this->shClient->listenEventsOnMsg();
		}
		$this->debug("fallen asleep on the 1 minute");
		sleep(60);
		$this->run($connection+1);
	}

	public function onGetEvent($data){
		//method called when event occurred
		foreach ($data as $value) {
			if($value["type"] != "" && in_array($value["type"], $this->allowedType)){
				//$this->debug(__METHOD__);
				$this->debug($value);

				$state = array();
				$tmpstate = $value["values"];
				$length = count($tmpstate);
				
		        if($length == 1){
		            $state["state"] = $tmpstate[0];
		        }else if($length == 2 && $value["type"] == "dimer-lamp"){
		            $state["state"] = $tmpstate[0];
		            $state["value"] = $tmpstate[1];
		        }else if($length == 6 && $value["type"] == "conditioner"){
		            $state["powerSwitch"] = (int)$tmpstate[0] & 0xf;
		            $state["temperature"] = (int)$tmpstate[1] + 16;
		            $state["air-fan"] = (int)$tmpstate[4] & 0xf;
		            $state["mode"] = (int)$tmpstate[0] >> 4;
		            $state["wide_vane"] = (int)$tmpstate[3] & 0xf;
		            $state["vane"] = (int)$tmpstate[3] >> 4;
		        }else {
		            $state = $tmpstate;
		        }
				$this->devicesStore[$value["addr"]] = $state;
			}
		}
		
		//$this->debug(json_encode($this->devicesStore));
	}
	
	private function connectToShS() {
		if(is_null($this->shClient)) {
				
			$this->debug(__METHOD__ . " " . __LINE__);
							
			$settings = $this->settings["shs"];
			$this->shClient = new \SHClient($settings["host"], $settings["port"], $settings["secret_key"], $settings["logFile"]);
			$this->shClient->debug = $this->debug;
			$this->shClient->listenEventsDelay = $this->settings["listenEventsDelay"];
			$this->shClient->readFromBlockedSocket = TRUE;
			
			if(!$this->shClient->run2()){
				$this->error = TRUE;

				if(count($this->shClient->errors)){
					foreach ($this->shClient->errors as $msg) {
						$this->msg = $msg;
						$this->debug();
						//$this->logger();
					}
				}
			}
			
		}elseif(!$this->shClient->clientIsConnected()) {
				
			$this->debug(__METHOD__ . " " . __LINE__);
			
			$this->shClient->clearErrors();
			
			if(!$this->shClient->run2()){
				$this->error = TRUE;

				if(count($this->shClient->errors)){
					foreach ($this->shClient->errors as $msg) {
						$this->msg = $msg;
						$this->debug();
						//$this->logger();
					}
				}
			}

		}
		
	}

	private function debug($msg = ""){
		if(is_array($msg)) $msg = print_r($msg, 1);
		elseif(!is_string($msg)) $msg = var_export($msg, TRUE);
		if($this->debug) print date("Y-m-d H:i:s") . "\t" . $msg . "\n";
	}

}


?>