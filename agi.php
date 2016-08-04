<?php
ob_implicit_flush(true); //This turns on implicit flushing, meaning PHP will flush the buffer after every output call. This is necessary to make sure that AGI scripts get their instructions to Asterisk as soon as possible, rather than buffering until script termination.

set_time_limit(6); //This sets the maximum execution time for the AGI script. I usually like to keep this set low (6 seconds), because the script should complete pretty quickly and the last thing we want it to do is hang a call because the script is churning.

set_error_handler("error"); //This sets a custom error handler function. We'll get back to this later.

$in = fopen("php://stdin","r"); //This creates a standard in that can be used by our script.

$stdlog = fopen("php://stderr", "w"); //This creates an access to standard error, for debugging.




function read() {  
    global $in, $debug, $stdlog;  
    $input = str_replace("\n", "", fgets($in, 4096));  
    if ($debug){  
        fputs($stdlog, "read: $input\n");  
    }  
    return $input;  
}

function write($line) {  
    global $debug, $stdlog;  
    if ($debug) {  
        fputs($stdlog, "write: $line\n");  
    }  
    echo $line."\n";  
}

function execute($command) {  
    global $in, $out, $debug, $stdlog;  
    write($command);  
    $data = fgets($in, 4096);  
    if (preg_match("/^([0-9]{1,3}) (.*)/", $data, $matches)) {  
        if (preg_match('/^result=([0-9a-zA-Z]*)( ?\((.*)\))?$/', $matches[2], $match)) {  
            $arr['code'] = $matches[1];  
            $arr['result'] = $match[1];  
            if (isset($match[3]) && $match[3]) {  
                $arr['data'] = $match[3];  
            }  
            if($debug) {  
                fputs($stdlog, "CODE: " . $arr['code'] . " \n");  
                fputs($stdlog, "result: " . $arr['result'] . " \n");  
                fputs($stdlog, "result: " . $arr['data'] . " \n");  
                fflush($stdlog);  
            }  
            return $arr;  
        } else return 0;  
    } else return -1;  
}

function verbose($str,$level=0) {  
    $str=addslashes($str);  
    execute("VERBOSE \"$str\" $level");  
}  

function error($errno,$errst,$errfile,$errline) {  
    verbose("AGI ERROR: $errfile, on line $errline: $errst");  
}

while ($env=read()) {  
    $s = split(": ",$env);  
    $key = str_replace("agi_","",$s[0]);  
    $value = trim($s[1]);  
    $_AGI[$key] = $value;  
    if($debug) {  
        verbose("Registered AGI variable $key as $value.");  
    }  
    if (($env == "") || ($env == "\n")) {  
        break;  
    }  
}

?>