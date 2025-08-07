<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$userNumber=$argv[1];
$testMode=true;
require_once('/var/www/html/programs/sqldata.php');
require_once ('/var/www/html/classes/MysqliDb.php');
require_once("/var/www/html/classes/RigServer.class.php");
require_once "/var/www/html/programs/GetSettingsFieldFromIDFunc.php";
$hamlib=getUserField($userNumber,"ID");
$com=getUserField($userNumber,"Port");
$man=getUserField($userNumber,"Manufacturer");
$mod=getUserField($userNumber,"Model");
$speed=getUserField($userNumber,"Baud");
$port=4530+$userNumber*2;
echo "Starting test for radio $userNumber ($man $mod with Hamlib number $hamlib).\n";
$execDum="rigctld -m $hamlib -r $com -s $speed > /dev/null 2>&1 &";
echo "Executing '".$execDum."'\n";
shell_exec($execDum);
usleep(100000);
echo "rigctl started.\n";
$rigServer = new RigServer("127.0.0.1",$port,$userNumber,$testMode);
echo "rigserver started, returned " . print_r($rigServer) . "\n";
usleep(100000);
$rigServer->infinite_loop();
?>