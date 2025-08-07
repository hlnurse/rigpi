<?php
include "/var/www/html/classes/PhpSerialClass.php";

// Let's start the class
$serialRelay = new PhpSerial;
// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
$serialRelay->deviceSet("\dev\ttyUSB0");
$serialRelay->confBaudRate(9600);
$serialRelay->confStopBits(1);
$serialRelay->confCharacterLength(8);

$serialRelay->deviceOpen();

$serialRelay->sendMessage("\xFF\x01\x00");

$serialRelay->deviceClose();

?> 