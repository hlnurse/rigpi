<?php

header('Access-Control-Allow-Origin: *');
include "./PhpSerial.php";
system("stty -F /dev/ttyUSB0 -icanon");
static $serial;
$serial = new PhpSerial();
error_reporting(E_ALL);
ini_set('display_errors','1');
$serial->deviceSet("/dev/ttyUSB0");
$serial->confBaudRate(1200);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");
$serial->deviceOpen();
$serial->sendMessage(chr(0x00) . chr(0x02));

$serial->sendMessage(chr(0x0F) . chr(0x04). chr(0x20). chr(0x05). chr(0x32). chr(0x00). chr(0x00). chr(0x05). chr(0x19). chr(0x00). chr(0x00). chr(0x00). chr(0x00). chr(0x32). chr(0x06). chr(0xff) . chr(0x07) . chr(0x15));
$x=0;
sleep(0.1);
	$t=$serial->readPort();
	if (strlen($t)>0){
	echo "before sleep 	in thread: $x " . bin2hex($t) . "\n\r";	
		
	}
 $serial->sendMessage("CQ ");
while ($x<=20)
{
	$t=$serial->readPort();
	if (strlen($t)>0){
	echo "in thread: $x " . bin2hex($t) . "\n\r";	
		
	}
    sleep(1);
    $x =$x+1;
}
echo "STOP" . "\n\r";
?>