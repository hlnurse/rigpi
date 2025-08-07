<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function disRadio($tMyRadio, $tUserName, $tMyRotor, $tMyInstance)
{
  error_log(
    date("Y-m-d H:i:s", time()) . " " . "closing radio" . PHP_EOL,
    3,
    "/var/log/rigpi-radio.log"
  );
 // $tMyInstance=4818;
  //$tMyInstance=$_SESSION['myInstance'];
if (strlen($tMyRadio)==0){
  $tMyRadio = 1;
}
if (strlen($tUserName)==0){
  $tUserName=$_SESSION['myUsername'];  //only if user not specified
}
if (strlen($tMyRotor)==0){
  $tMyRotor=1;
}
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/GetMyRadioFunc.php";
require_once $dRoot . "/programs/GetSettingsFunc.php";
$tRadioNum = require_once $dRoot . "/programs/GetSelectedRadioInc.php";
$radioPort=GetField($tMyRadio,"Port","MySettings");

//$id=GetField($tMyRadio,"ID","MySettings");
$service_port=0;
$service_host="";
$RadioSerialPort="";
//port can be like:
  //dev/ttyUSB0
  //4532
  //localhost:4532
  //172.16.0.62:4532
if (strpos($radioPort, ":")>1){  //for a remote connection
  $aPort=explode(":",$radioPort);
  $service_host=$aPort[0];
  $service_port=$aPort[1];
//  doLog($test, "PORT now: $service_port and HOST now: $service_host  \n");
}elseif ($radioPort>4530 && $radioPort < 5000){ //for a local connection
  $service_port=$radioPort;
  $service_host="0.0.0.0";
}else{  //for a local physical connection
  $service_host="0.0.0.0";
  $service_port=$tMyRadio*2 + 4530;
  if (strpos($radioPort,"/dev/")>-1){
    $RadioSerialPort=$radioPort;
  }
}
if (strlen($RadioSerialPort)==0){
  $RadioSerialPort="$service_host:$service_port";  //this can now be used in -r parameter for rigctl
}
$rotorPort=GetField($tMyRadio,"RotorPort","MySettings");
$tTable = "RadioInterface";
$tField = "CWIn";
$tData = "<18><00><02><00>";
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require_once( $dRoot . "/programs/SetSettingsFunc.php");
require $dRoot . "/programs/sqldata.php";
//sets cw PTT off
$sQuery = "update $tTable set $tField = concat($tField, '$tData') where Radio='$tMyRadio'";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$result = $con->query($sQuery);
//$killme='pkill rigctld';
//exec($killme);
//return;
SetInterface($tMyRadio, "PTTOut", 0);
$tTable = "RadioInterface";
$tField = "PTTIn";
$tData = "0";
//SetInterface($tMyRadio, "CommandOut", "q");
usleep(100000);
$tR = "'[R]igDo.php '";
$users = exec("ps aux | grep $tR | grep $tMyInstance");
$tRigDoPID = substr($users, 9, 5);
$tR = "'[T]CPDo.php $tUserName [r]adio$tMyRadio'";
$users = exec("ps aux | grep " . $tR);
$tTCPDoPID = substr($users, 9, 5);
$tR = "'[C]WDo.php'";
$users = exec("ps aux | grep $tR | grep $tMyInstance");
$tCWDoPID = substr($users, 9, 5);

//echo $users . " " . "\n";
$tR = "'[U]DPDo.php $tUserName [r]adio$tMyRadio'";
$users = exec("ps aux | grep " . $tR);
$tUDPDoPID = substr($users, 9, 5);
$tR = "'[R]otorDo.php'";
$users = exec("ps aux | grep $tR | grep $tMyInstance");
$tRotorDoPID = substr($users, 9, 5);
$tRPort = $tMyRadio * 2 + 4530;
$tR = "'[G]PIOInt1.php'";
$users2 = exec("ps aux | grep " . $tR);
$tRemCWPID = substr($users2, 9, 5);
$users1 = exec("ps aux | grep '[r]igctld' | grep '[i]nstance' | grep $tMyInstance");
$trigctldPID1 = substr($users1, 9, 5);
    error_log(
  date("Y-m-d H:i:s", time()) . " user1: " . $users1 . PHP_EOL,
  3,
  "/var/log/rigpi-radio.log"
);

$tR = "'[r]otctld'";
$users = exec("ps aux | grep $tR  | grep $tMyInstance");
$trotctldPID = substr($users, 9, 5);
//$users2 = exec("ps aux | grep '[i]nstance' | grep $tMyInstance");
// $trigctldPID1 = substr($users2, 9, 5);
//echo "2" . $tMyInstance . " " . $trigctldPID1 . "\n";
$trigctlTest=$trigctldPID1;
if ($trigctldPID1 > 0) {
 // echo "1 ".$trigctldPID1;
    posix_kill($trigctldPID1, 15);
    SetInterface($tMyRadio, "MainOut", "OFF");
    SetInterface($tMyRadio, "SubOut", "OFF");
    SetInterface($tMyRadio, "MainIn", "OFF");
    SetInterface($tMyRadio, "SubIn", "OFF");
    usleep(5000);
  }
      $users3 = exec("ps aux | grep '[i]nstance' | grep $tMyInstance");
      $trigctldPID1 = substr($users3, 9, 5);
//echo "3" . $tMyInstance . " " . $trigctldPID1 . "\n";;
sleep(1);
if ($trigctldPID1 > 0) {
     posix_kill($trigctldPID1, 15);
   };
//return;
 // echo "2 ".$trigctldPID1;
 
    $users = exec("ps aux | grep '[i]nstance' | grep $tMyInstance");
     $trigctldPID1 = substr($users, 9, 5);
     if ($trigctldPID1 > 0) {
      posix_kill($trigctldPID1, 15);
    };
    $users = exec("ps aux | grep '[i]nstance' | grep $tMyInstance");
      $trigctldPID1 = substr($users, 9, 5);
      if ($trigctldPID1 > 0) {
        posix_kill($trigctldPID1, 15);
      };

if ($tTCPDoPID > 0) {
  posix_kill(trim($tTCPDoPID), 15);
}
$tR = "'[R]igDo.php '";
if ($tRigDoPID > 0) {
  posix_kill(trim($tRigDoPID), 15);
  $users = exec("ps aux | grep $tR | grep $tMyInstance");
  while ($users){
    $tRigDoPID = substr($users, 9, 5);
    posix_kill(trim($tRigDoPID), 15);   
    $users = exec("ps aux | grep $tR | grep $tMyInstance");
  }
}
$tR = "'[C]WDo.php '";
if ($tCWDoPID > 0) {
  posix_kill(trim($tCWDoPID), 15);
  $users = exec("ps aux | grep $tR | grep $tMyInstance");
  while ($users){
    $tCWDoPID = substr($users, 9, 5);
    posix_kill(trim($tCWDoPID), 15);   
    $users = exec("ps aux | grep $tR | grep $tMyInstance");
  }
}
$tR = "'[r]otctld.php '";
if ($trotctldPID > 0) {
  posix_kill(trim($trotctldPID), 15);
  $users = exec("ps aux | grep $tR | grep $tMyInstance");
  while ($users){
    $trotctldPID = substr($users, 9, 5);
    posix_kill(trim($trotctldPID), 15);   
    $users = exec("ps aux | grep $tR | grep $tMyInstance");
  }
}
SetInterface($tMyRadio, "MainOut", "OFF");
SetInterface($tMyRadio, "SubOut", "OFF");
SetInterface($tMyRadio, "MainIn", "OFF");
SetInterface($tMyRadio, "SubIn", "OFF");
usleep(5000);
if ($tCWDoPID > 0) {
//  echo "kill $tCWDoPID";
  posix_kill(trim($tCWDoPID), 15);
}
if ($tUDPDoPID > 0) {
  posix_kill(trim($tUDPDoPID), 15);
}
if ($tRemCWPID > 0) {
//    exec("kill $tRemCWPID");
  $tK = "sudo /usr/share/rigpi/nocw.sh " . $tRemCWPID;
  exec($tK);
}
$tR = "'[R]otorDo.php'";
if ($tRotorDoPID > 0) {
  posix_kill(trim($tRotorDoPID), 15);
  $users = exec("ps aux | grep $tR | grep $tMyInstance");
  while ($users){
    $tRotorDoPID = substr($users, 9, 5);
    posix_kill(trim($tRotorDoPID), 15);   
    $users = exec("ps aux | grep $tR | grep $tMyInstance");
  }
}

usleep(1000000);
if ($trigctlTest > 0) {
  echo "<br>&nbsp;&nbsp;Radio " .
    $tMyRadio .
    " (for " .
    $tUserName .
    " account) is now disconnected.<br><br>";
    
} else {
  echo "<br>&nbsp;&nbsp;Radio connection " .
    $tMyRadio .
    " was already disconnected.<br><br>";
}

//return;
}
?>
