#!/usr/bin/php -q
<?php
{
	//argv[1] = phone number
	//argv[2] = application param

	// If the application is having problems you can log to this file
	$parm_error_log = '/tmp/wakeup.log';
	
	// Set to 1 to turn on the log file
	$parm_debug_on = 1;	
	
	// This is where the Temporary WakeUp Call Files will be created
	$parm_temp_dir = '/tmp';

	// channel name
	$channel = 'PJSIP/';
	
	// This is where the WakeUp Call Files will be moved to when finished
	$parm_call_dir = '/var/spool/asterisk/outgoing';

	// Aplication name
	$parm_application = 'AGI';
	
	// Aplication  param
	$parm_data = '/home/katod/projects/PHP_GSM/GSM.php';

	// How many times to try the call
	$parm_maxretries = 2;
	
	// How long to keep the phone ringing
	$parm_waittime = 10;
	
	// Number of seconds between retries
	$parm_retrytime = 10;
	
	// Caller ID of who the wakeup call is from Change this to the extension you want to display on the phone
	$parm_callerid = '"Larnitech"<777>';

	$filename = '$channel$argv[1].call';

	$wuc = fopen($parm_temp_dir.'/'.$filename, 'w');

	print_r($argv);
	
	// if ( $parm_chan_ext )
	// 	fputs( $wuc, "channel: $chan/$sta\n" );
	// else
		fputs( $wuc, "channel: $channel$argv[1]\n" );		
		fputs( $wuc, "maxretries: $parm_maxretries\n");
		fputs( $wuc, "retrytime: $parm_retrytime\n");
		fputs( $wuc, "waittime: $parm_waittime\n");
		fputs( $wuc, "callerid: $parm_callerid\n");		
		fputs( $wuc, "application: $parm_application\n");
		fputs( $wuc, "data: $parm_data,$argv[2]\n");

	fclose($wuc); //close file

	rename("$parm_temp_dir/$filename", "$parm_call_dir/$filename");

	echo "DONE";
}
?>
