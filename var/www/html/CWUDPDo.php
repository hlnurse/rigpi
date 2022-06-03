<?php

require_once("/var/www/html/classes/CWUDPServer.class.php");
require_once("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/programs/SetLogFunc.php");
require_once("/var/www/html/programs/GetSettingsFunc.php");
require_once("/var/www/html/classes/MysqliDb.php");
require_once("/var/www/html/programs/GetUserFieldFunc.php");
require_once("/var/www/html/programs/SetSettingsFunc.php");

$tUser=$argv[1];
$tMyRadio=$argv[2];
$tMyPort=$argv[3];
//$tInvert=$argv[4];
$tMyIP="127.0.0.1"; // note this is the LOCAL server waiting for a connection, must be localhost
$tMyRadio=substr($tMyRadio, strlen($tMyRadio)-1);
$arKey=array();
$tAvTime=0;
$tAvCount=0;
echo "starting CW UDPServer\n";
require ('/var/www/html/programs/vendor/autoload.php');
system ( "gpio mode 13 out");
system ("gpio write 13 1");	 //key up

$UDPServer = new CWUDPServer($tMyIP,$tMyPort, $tMyRadio, $tUser);
$UDPServer->max_clients = 10; // Allow no more than 10 people to connect at a time
$UDPServer->hook("CONNECT","handle_connect"); // Run handle_connect every time someone connects
$UDPServer->hook("INPUT","handle_input"); // Run handle_input whenever text is sent to the server
$UDPServer->hook("CLOCK","handle_clock"); // Run handle_clock whenever text is sent to the server

$UDPServer->infinite_loop(); // Run CW Server Code Until Process is terminated.

function handle_connect(&$server,&$client,$input,$call,$radio)
{
	echo "CW connected!" . "\n";
	system ("gpio write 13 1");	 //key up
}

//function send()
function handle_clock(&$server,&$client,$input,$call,$radio)
{
}

function handle_input(&$server,&$client,$input,$radio,$user)
{
	$input=$input." ". floor(1000*microtime(true));
	$out=explode(" ",$input);
	if ($GLOBALS['tAvCount']<11){   //only first 10 events are used to set average
		$GLOBALS['tAvCount']=$GLOBALS['tAvCount']+1;
		$GLOBALS['tAvTime']+=($out[2]-$out[1]);
	}
	$av=floor($GLOBALS['tAvTime']/$GLOBALS['tAvCount']); //this is the average delay, will account for differences in clocks in 2 RPi's
	$td=floor(10+$av-($out[2]-$out[1]));  //10 -  '...' 10 is delay in milliseconds max to allow for jitter;
	if ($td<0){
		$td=0;
	}
	usleep(intval($td*1000));
	if ($out[0]==1){
		system ("gpio write 13 0");  //key down
	}else{
		system ("gpio write 13 1");	 //key up
	}
}

?>