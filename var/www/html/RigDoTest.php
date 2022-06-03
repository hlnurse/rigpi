<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$tUser='admin';//$argv[1];
$tMyRadio='radio1';//$argv[2];
$port=4532;//$argv[3];
$test=0;//$argv[4];
$vfo='VFOA';//$argv[5];
$tMyRadio=substr($tMyRadio, strlen($tMyRadio)-1);
require_once('/var/www/html/programs/sqldata.php');
require_once ('/var/www/html//classes/MysqliDb.php');
require_once("/var/www/html/classes/RigServer.class.php");
$rigServer = new RigServer("127.0.0.1",$port,$tMyRadio,$test,$vfo);
usleep(100000);
$rigServer->infinite_loop();
?>