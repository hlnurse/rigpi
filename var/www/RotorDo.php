<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$tUser=$argv[1];
$tMyRotor=$argv[2];
$port=$argv[3];
$test=$argv[4];
$tMyRotor=substr($tMyRotor, strlen($tMyRotor)-1);
require_once('/var/www/html/classes/RotorServer.class.php');
$rotorServer = new RotorServer("127.0.0.1",$port,$tMyRotor,$test);
usleep(100000);
$rotorServer->infinite_loop();
?>