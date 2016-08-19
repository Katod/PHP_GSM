#!/usr/bin/php5
<?php
//Пример 2
//Включить, выключить устройство

include_once "settings.php";

$controller = new Device($globalSettings);
//$controller->setDeviceState("771:7");
$state = $controller->getDeviceState("531:16");
print_r($state);

echo "second";
$state = $controller->getDeviceState("531:8");
print_r($state);

echo "finish";
//$controller->debug($state);

class Device {

	public $error = FALSE;
	public $shClient = NULL;
	public $itemAddr = "";
	public $itemState = array();
	public $debug = TRUE;
	public $settings = array();
	public $logFile = "";

	public function __construct($settings) {

		$this->settings = $settings;
		$this->logFile = $this->settings["logFile"];
		$this->debug = $this->settings["debug"];
	}

	public function __destruct(){
		//$this->debug(__METHOD__);
	}
	    
    public function setDeviceState($addr, $state = NULL) {
        
        if(strpos($addr, ":") === FALSE) {
			$this->error = true;
			$this->msg = $this->translate("Device address not received");
        }
		
        if(!$this->error && !is_null($state)){
	        if(!is_array($state) || (is_array($state) && !array_key_exists("state", $state))){
				$state = NULL;
	        }
		}
        
        if(!$this->error) $this->connectToShS();
		
		if(!$this->error){
            if(is_null($state)) $state = array("state"=>0xff);
			$this->itemAddr = $addr;
			$this->itemState = $state;

			$methodName = "onSetDeviceState";
			$callback = array($this, $methodName);

        	$this->shClient->sendCommandToSH('retranslate-udp', $callback);
			$this->shClient->listenEventsOnMsg();
        }
		
    }

    public function getDeviceState($addr) {
    	$state = NULL;
        if(strpos($addr, ":") === FALSE) {
			$this->error = true;
			$this->msg = $this->translate("Device address not received");
        }
        if(!$this->error) $this->connectToShS();
		if(!$this->error) $state = $this->shClient->getDeviceStateByAddr($addr);
        
		return $state;
    }

	public function onSetDeviceState($xml){
		$this->debug(__METHOD__ . " line: " . __LINE__);
		$this->shClient->stopListenEvents();
		$this->debug($this->itemAddr);
		$this->debug($this->itemState);
		$this->shClient->setDeviceStateByAddr($this->itemAddr, $this->itemState);
	}

	public function debug($msg = ""){
		if(is_array($msg)) $msg = print_r($msg, 1);
		elseif(!is_string($msg)) $msg = var_export($msg, TRUE);
		if($this->debug) print date("Y-m-d H:i:s") . "\t" . $msg . "\n";
	}

	private function connectToShS() {
		if(is_null($this->shClient)) {
				
			$this->debug(__METHOD__ . " " . __LINE__);
							
			$settings = $this->settings["shs"];
			$this->shClient = new \SHClient($settings["host"], $settings["port"], $settings["secret_key"], $settings["logFile"]);
			$this->shClient->debug = $this->debug;
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

}



?>