<?php
/*
 * RigPi h.php: starts hamlib rigctld
 *
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * The radio can be Hamlib Dummy or Hamlib net rigctl or a physical radio.
 * The rigpi server class connects to this rigctl or rigctld.
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$tr="";
$tu="";
$dRoot = "/var/www/html";
if (isset($argc)){
  if ($argc==2){
    $tr=$argv[1];
    $tu=$argv[2];
  }
}else{
  $tr=1;
  $tu='admin';
  require_once $dRoot . "/liteSettings.php";
}
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
require_once $dRoot . "/programs/disconnectRadioFunc.php";
require_once $dRoot . "/programs/SetSettingsFunc.php";

function doLog($utest, $what)
{
  if ($utest == 1) {
    error_log(
      date("Y-m-d H:i:s", time()) . " " . $what . PHP_EOL,
      3,
      "/var/log/rigpi-radio.log"
    );
  };
};

if (isset($_POST["startUpDelay"])){
  $delay=$_POST["startUpDelay"];
  usleep($delay * 1000000);
}

if (isset($_POST["radio"])) {  //test check
  $test = $_POST["test"]; 
} else {
  $test = 1; 
}
//$test=1;
if ($test == 1) {
  echo "TEST MODE\n\n";
}
$useVFOMode = 0;
$tRigPid = 0;
$report = "";
$reportOut = "";
if (isset($_POST["radio"])) {
  $tMyRadio = $_POST["radio"];
} else {
  if (strlen($tr)>0){
    $tMyRadio=$tr;
  }else{
    $tMyRadio =1;
  }
}
$_SESSION['myInstance']='1234' . $tMyRadio;
$instance=$_SESSION['myInstance'];
if (isset($_POST["user"])) {
  $tUsername = $_POST["user"];
} else {
  if (strlen($tu)>0){
    $tUsername = $tu; // //only if user not specified
  }else{
    $tUsername='admin';
  }
}
  $_SESSION['myUsername']=$tUsername;

if ($test==1){
  echo $tMyRadio . " " . $tUsername . "\n";
}

if (isset($_POST["keyer"])) {
  $tMyKeyer = $_POST["keyer"];
} else {
  $tMyKeyer = "rpk";
}
if (isset($_POST["port"])) {
  $tMyCWPort = $_POST["port"];
} else {
  $tMyCWPort = "/dev/ttyS0";
}
if (isset($_POST["rotorPort"])) {
  $tMyRotorPort = $_POST["rotorPort"];
} else {
  $tMyRotorPort = "none";
}
if (isset($_POST["keyerPort"])) {
  $tMyUDPKeyerPort = $_POST["keyerPort"];
} else {
  $tMyUDPKeyerPort = 30041;
}
if (isset($_POST["keyerIP"])) {
  $tMyKeyerIP = $_POST["keyerIP"];
} else {
  $tMyKeyerIP = "127.0.0.1";
}
if (isset($_POST["keyerFunc"])) {
  $tMyKeyerFunc = $_POST["keyerFunc"];
} else {
  $tMyKeyerFunc = 0;
}
if (isset($_POST["tcpPort"])) {
  $tMyTCPPort = 30001; //this is adjusted later. $_POST["tcpPort"];
} else {
  $tMyTCPPort = 30001;
}
if (isset($_POST["UDPPort"])) {
  $tMyUDPPort = $_POST["UDPPort"];
} else {
  $tMyUDPPort = 2333;
}
$tMyTCPPort = $tMyTCPPort + ($tMyRadio - 1); //allows for connection to any account
$report = "Radio: " . $tMyRadio . PHP_EOL;
$report .= "User: " . $tUsername  . PHP_EOL;
$report .= "Instance: " . $instance  . PHP_EOL;
$tClear = "cat /dev/null > /var/log/rigpi-radio.log";
exec($tClear);
$tClear = "cat /dev/null > /var/log/rigpi-rotor.log";
exec($tClear);
doLog($test, PHP_EOL . PHP_EOL . "RIGPI RADIO DIAGNOSTIC LOG" . PHP_EOL);
doRotorLog($test, PHP_EOL . PHP_EOL . "RIGPI ROTOR DIAGNOSTIC LOG" . PHP_EOL);
//Get data from MyRadio table
//SetInterface($tMyRadio, "MainIn", "OFF");
//SetInterface($tMyRadio, "MainOut", "OFF");

$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$dRadio = $row["Radio"];
$report .= "Radio from settings: " . $dRadio . PHP_EOL;
$report .= "Instance: " . $instance . PHP_EOL;
$dmodel = $row["Model"];
$dPower=$row["PowerControl"];
$radioPort = $row["Port"];
$tMyRotorPort=$row["RotorPort"];
$keyerPort = $row["KeyerPort"];
if ($test==1){
  echo "radioPort=$radioPort\n";
}
$tMyRotor = $row["Rotor"];
$rotorID = $row["RotorID"];
$rotorName = $row["RotorModel"];
$rotorBaud = $row["RotorBaud"];
$rotorStop = $row["RotorStop"];
doLog ($test, "radio PORT: ". $radioPort);
//do config setup for radio
$setConf="";
if ($dPower=='Manual'){
  $setConf="";
}elseif($dPower=="Auto Power On"){
  $setConf="-C auto_power_on=1";
}elseif($dPower=="Auto Power Off"){
  $setConf="-C auto_power_off=1";
}else{
  $setConf="-C auto_power_on=1,auto_power_off=1";
}

$tMyTCPPort = $tMyTCPPort + ($tMyRadio - 1); //allows for connection to any account

if ($rotorBaud == "default") {
  $rotorBaud = "";
} else {
  $rotorBaud = "-s " . $rotorBaud;
}

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
  doLog($test, "PORT now: $service_port and HOST now: $service_host  \n");
}elseif ($radioPort>4530 && $radioPort < 5000){ //for a local connection
  $service_port=$radioPort;
  $service_host="0.0.0.0";
}else{  //for a local physical connection
  $service_host="0.0.0.0";
  $service_port=$dRadio*2 + 4530;
  if (strpos($radioPort,"/dev/")>-1){
    $RadioSerialPort=$radioPort;
  }
}
if (strlen($RadioSerialPort)==0){
  $RadioSerialPort="$service_host:$service_port";  //this can now be used in -r parameter for rigctl
}
//$host='172.16.0.12';
//$radioPort=4532;
//$service_port = $radioPort;
//echo "early service_port=$service_port\n";
//$service_host = $host;
//echo "early service_host=$service_host\n";
//$tMyRadioPort = $service_port;
//if ($tMyRadioPort > 4530 && $tMyRadioPort < 5000) {
  $tMyCWRadio = $dRadio; 
//} else {
//  $tMyCWRadio = $dRadio;
//}
if ($test == 1) {
  echo $report;
  $u = "radio: " . $tMyRadio . " connection attempt in h" . PHP_EOL;
  $u .= "	radio port: " . $RadioSerialPort . PHP_EOL;
  $u .= "	keyer: " . $tMyKeyer . PHP_EOL;
  $u .= "	instance: " . $instance . PHP_EOL;
  $u .= "	username: " . $tUsername . PHP_EOL;
  $u .= "	cw port: " . $tMyCWPort . PHP_EOL;
  $u .= "	keyer UDP port (remote): " . $tMyUDPKeyerPort . PHP_EOL;
  $u .= "	keyer IP: " . $tMyKeyerIP . PHP_EOL;
  $u .= "	keyer: " . $tMyKeyer . PHP_EOL;
  $u .=
    "	keyer function: " .
    $tMyKeyerFunc .
    " (0=normal,1=radio,2=remote, 3=CTS)" .
    PHP_EOL;
  $u .= "	tcp port: " . $tMyTCPPort . PHP_EOL;
  $u .= "	udp port: " . $tMyUDPPort . PHP_EOL;
  $u .= "	dRadio: " . $dRadio . PHP_EOL;
  $u .= "	tMyCWRadio: " . $tMyCWRadio . PHP_EOL;
  $u .= "	vfoMode: " . $useVFOMode . PHP_EOL;
  $u .= "   rotor: " . $tMyRotor . " connection attempt" . PHP_EOL;
  $u .= "	rotor port: " . $tMyRotorPort . PHP_EOL;
  $u .= "	rotor ID: " . $rotorID . PHP_EOL;
  $u .= "	rotor Name: " . $rotorName . PHP_EOL;
  $u .= "	rotor Baud: " . $rotorBaud . PHP_EOL;
  $u .= "	rotor Stop: " . $rotorStop . PHP_EOL;
  echo $u;
  echo "More information is in /var/log/rigpi-radio.log and /var/log/rigpi-rotor.log<br>";
}

$report .= "Port from settings: " . $radioPort  . PHP_EOL;
$report .= "Instance: " . $instance  . PHP_EOL;
$id = $row["ID"];
$ptt = $row["PTTMode"];
$tInvert = $row["KeyerInvert"];
$db->where("NUMBER", $id);
$row1 = $db->getOne("Radios");
if ($row1["SAMEAS"] > 0) {  //look for alternate equivalent
  $report .=
    "ID from SAMEAS: " . $id . " is same as " . $row1["SAMEAS"] . "<br>";
  $id = $row1["SAMEAS"];
} else {
  $report .= "ID from settings: " . $id . PHP_EOL;
}
$tBaud = $row["Baud"];
if ($tBaud == "default") {
  $baud = "";
} else {
  $baud = "-s $tBaud";
}

if ($ptt == 2) {
  $ptt = "-P GPIO -p 17";
} else {
  $ptt = "";
}
$stop = $row["Stop"];
if ($stop == 'default' || $stop == '') {
  $stop = "";
} else {
  if ($setConf==""){
    $setConf="-C stop_bits=".$stop;
  }else{
    $setConf .= ",stop_bits=".$stop;
  }
}
if (isset($_SESSION['myInstance'])){
  $instance=$_SESSION['myInstance'];
}
//    $instance=4818;
$_SESSION['myInstance']=$instance;
  if (!isset($instance) || $instance==""){
///////////////    $instance=4818;
  }
if ($setConf==""){
    $setConf="-C instance=".$instance;
  }else{
    $setConf .= ",instance=".$instance;
  }

//echo "SETCONF: ". $setConf . "\n";
$tRTS = $row["RTS"];
if (strlen($tRTS) > 4) {
  $tRTS = "";
} else {
  if ($tRTS == "high") {
    $tRTS = "ON";
  } else {
    $tRTS = "OFF";
  }
  if ($setConf==""){
    $setConf="-C rts_state=".$tRTS;
  }else{
    $setConf .= ",rts_state=".$tRTS;
  }
}

$tDTR = $row["DTR"];
if (strlen($tDTR) > 4) {
  $tDTR = "";
} else {
  if ($tDTR == "high") {
    $tDTR = "ON";
  } else {
    $tDTR = "OFF";
  }
  if ($setConf==""){
    $setConf="-C dtr_state=".$tDTR;
  }else{
    $setConf .= ",dtr_state=".$tDTR;
  }
}

if (strlen($row["CIV_Code"]) == 7) {
  $tCIV = "";
} else {
  $tCIV = "--civaddr " . $row["CIV_Code"];
  if (stripos($tCIV, "H") > 0) {
    $tCIV = str_replace("H", $tCIV, "");
    $tCIV = str_replace("h", $tCIV, "");
  }
}
//$testVFO="rigctl -m 2 -r $service_host:$service_port -u | grep '[V]FO list -m 1'";
$testVFO="rigctl -m $id -u | grep '[V]FO list -m 1'";
$vfos = shell_exec($testVFO);
//echo "HERE $testVFO $vfos\n";
if (strstr($vfos, "VFOA")) {
  $vfoa = "VFOA";
  $vfob = "VFOB";
} else {
  $vfoa = "Main";
  $vfob = "Sub";
}
$vfoMode = 0;
$vfoParam = "";
if ($useVFOMode == 1) {
  $vfoMode = 1;
  $vfoParam = "-o";
}
// Get the PID .
$db->where("Username", $tUsername);
$row = $db->getOne("Users");
$tRigDoPID = $row["rigDoPID"];
$report .= "RigDo.php rigDoPID from settings: " . $tRigDoPID . PHP_EOL;

if ($test == 1) {
  echo $report;
}
//if ($id == 2) {
//  $service_port = $port;
//} else {
//  $service_port = $tMyRadioPort;
//}
$report = "service_port: $setConf " . $service_port . PHP_EOL;
if ($test == 1) {
  doLog($test, $report);
  echo $report;
}
$tR = "'RigDo.php $tUsername [r]adio$tMyRadio'";
$users = exec("ps aux | grep " . $tR);
$pos = stripos($users, "radio" . $tMyRadio);
//$id=3073;
$tMyRadio=1;
$tMyRotor=1;
///$instance=4818;
//echo 'XXXXXXXXXXXXXXXX' . $id . " " . $tMyRadio  . " " . $tUsername  . " " . $tMyRotor  . " " . $instance . "\n";
//if ($id != "2"){
// echo "Running disRadio\n";
//   disRadio($id, $tMyRadio, $tUsername, $tMyRotor, $instance); /////////////////////////////////////////
//    echo "Ran disRadio\n";
//}
sleep(2);
//first Hamlib radios: 1 = dummy and 2 = net rigctl for connecting to an existing rigctld.
if ($id == "1" || $id == "2") {
  $execDum = "rigctld -m 1 -P RIG -T $service_host -t $service_port $ptt $vfoParam -C instance=$instance > /dev/null 2>/dev/null &";
//  		$report.=$result;
  $r = "";
  if ($id == "1") {
    $r = "Hamlib Dummy";
  } else {
    $execDum = "rigctl -m 2 -r $RadioSerialPort $vfoParam -P RIG -C instance=$instance > /dev/null 2>/dev/null &";
    if ($test==1){
      echo "execdum: $execDum\n";
    }  
    $r = "Hamlib Netctl";
    if (!$radioPort > 4530 && $test==1) {
      $report .= $r . ": Set Radio port to 4530 + 2 * account number.\n";
      echo $report;
 ////     exit();
    }
  }
  doLog($test, "$r radio $id start attempt: " . $execDum);
  $report = "$r radio $id start attempt: " . $execDum  . PHP_EOL;
  doLog($test, "Starting rigctld from exec (0).");
  exec($execDum);
  if ($test == 1) {
    $report .=
      "To see any error details, click Disconnect Radio, then start rigctl or rigctld in Terminal with this line: <br><br><b>" .
      $execDum .
      "</b><br><br>";
    echo $report;
    if ($id == 2) {
      $rd = shell_exec("ps aux | grep '[r]igctl -m $id'");
    } else {
      $rd = shell_exec("ps aux | grep '[r]igctld -m $id '");
    }
    $rd = str_replace("www", "<br>www", $rd);
    if (strlen($rd) == 0) {
      if ($id == 1) {
        $rd = "NONE (rigctld failed to start)";
        echo "<br>rigctld processes running: <br>" . $rd . "<br><br>";
      } else {
        $rd = "NONE (rigctl failed to start)";
        echo "<br>rigctl processes running: <br>" . $rd . "<br><br>";
      }
    }
  }
} else { //normal non-hamib radio
  doLog($test, "Starting rigctld from exec (1).");
  if ($test==1){
    echo("Starting rigctld from exec (1).");
  }
/////  if ($service_host==""){
//   $execMe = "rigctld -m $id -r $radio_port -T 0.0.0.0 -t 4532 $ptt $baud $tCIV $stop $vfoParam $setConf > /dev/null 2>/dev/null &"; 
 //    $execMe = "rigctld -m $id -r /dev/ttyUSB0 -T 0.0.0.0 -t $service_port $ptt $baud $tCIV $stop $vfoParam $setConf > /dev/null 2>/dev/null &"; 
/////    }else{
  //    $execMe = "rigctld -m $id -r $service_host:$service_port -T 0.0.0.0 -t 4532 $ptt $baud $tCIV $stop  $vfoParam $setConf > /dev/null 2>/dev/null &";
      $execMe = "rigctld -m $id -r $RadioSerialPort -T $service_host -t $service_port $ptt $baud $tCIV $stop $vfoParam $setConf > /dev/null 2>/dev/null &";
/////  }
$test=1;
  if ($test==1){
    echo "\nExecMe: " . $execMe . "\n";
  }
  doLog($test, "rigctl exec: " . $execMe);
  exec($execMe);
  doLog($test, "...execMe is done: $execMe");

  $freq=0;
  $i=0;
  sleep(1);
  doLog($test, "Getting freq");
    $id=2;
    //try for frequency
 $doExec="rigctl -m $id -r $service_host:$service_port f\n";// . PHP_EOL . $freq . PHP_EOL;
  doLog($test,$doExec);
  while ($freq==0 && $i<20){
    $i=$i+1;
    $freq=exec($doExec);
    doLog($test,"get freq: " . $freq . " from $doExec");
    if ($test==1){
      echo $i . " " . $doExec . " " . $freq . PHP_EOL;
    }
    usleep(1000000);
  }
  echo $freq;
}
/////ROTOR next////
   if (strpos($tMyRotorPort, ":")>0){
    $tR=explode(":", $tMyRotorPort);
    $tRotorPort=$tR[1];
    $tRotorIP=$tR[0];
  }elseif(strpos($tMyRotorPort, "dev")>0){
    $tMyRotorSerialPort=$tMyRotorPort;
    $tRotorIP = '0.0.0.0';
    $tRotorPort=$service_port+1;
  }else{
    $tRotorIP='0.0.0.0';
    $tRotorPort=$tMyRotorPort;
  }
if ($test==1){
    echo "RP: " . $tMyRotorPort . "\n"; //. " " . $tMyRadioSerialPort . "\n";
  }
if ($rotorID == "1" || $rotorID == "2") {
  $tRotorPort = $tMyRotorPort;
  $report .= "Rotor: " . $rotorID . " (" . $rotorName . ")<br>";
  $report .= "Rotor port: " . $tMyRotorPort . "<br>";
  doRotorLog(
    $test,
    "Now starting RigPi Rotor Server on port: $tRotorPort using rotor " .
      $rotorID .
      "."
  );
 // $tRotorIP="";
 // $tRotorPort=0;
  if ($rotorID == "1") {
    $r = "Hamlib Rotor Dummy";
//    if ($tMyRotorPort == "None") {
    $tRotorIP='0.0.0.0';
    $tRotorPort=$service_port+1;

/*      $report .=
        "Error: Set " .
        $rotorName .
        " Rotor port to 4531 + 2 * Radio number.\n";
      doRotorLog(
        $test,
        "Error: Set " . $rotorName . " Rotor port to 4531 + 2 * Radio number."
      );
      echo $report;
      exit();
    }
    */
    $execDum =
    "rotctld -m 1 -T $tRotorIP -t $tRotorPort -C instance:$instance" .  //////////////////
    " > /dev/null 2> /dev/null &";
   } else {
    $r = "Hamlib Rotor Netctl rotctl";
    if (!$tMyRotorPort > 4532 || $tRotorPort == "None") {
      $report .=
        "Error: Set " .
        $rotorName .
        " Rotor port to 4531 + 2 * other Radio number.\n";
      if ($test==1){
        echo $report;
      }
      doRotorLog($test, $report);
 ////     exit();
    }
    $execDum =
//    "rotctl -m 2 -r 172.16.0.28:4533" .
    "rotctl -m 2 -r $tRotorIP:$tRotorPort -C instance:$instance" .
    " > /dev/null 2> /dev/null &";
  }
  $report .= "Rotor ID $rotorID (" . $rotorName . ") start attempt.<br>";
  doRotorLog($test, "Rotor ID $rotorID (" . $rotorName . ") start attempt.");
  doRotorLog($test, "exec: $execDum\n");
  shell_exec($execDum);
  if ($test == 1) {
    $report .=
      "To see any error details, click Disconnect Radio, then start rotctl in Terminal with this line: <br><br><b>" .
      "rotctl -m 1" .
      "</b><br>";
    if ($test==1){
      echo $report;
    }
    $rd = shell_exec("ps aux | grep '[r]otctld -'");
    $rd1 = $rd;
    $rd = str_replace("www", "<br>www", $rd);
    if (strlen($rd) == 0) {
      $rd = "NONE (rotctld failed to start)";
    }
    if ($test==1){
      echo "<br>rotctld processes running: <br>" . $rd . "<br><br>";
    }
    doRotorLog($test, "rotctld processes running: " . PHP_EOL . "    " . $rd1);
  }
} else  {
//$tRotorPort=4533;
 $execMe = "rotctld -m $rotorID -r $tMyRotorSerialPort -T $tRotorIP -t $tRotorPort $rotorBaud -C instance=$instance > /dev/null 2>/dev/null &";
 // $execMe = "rotctld -m $rotorID -r /dev/ttyUSB0 -T 0.0.0.0 -t 4533 $rotorBaud > /dev/null 2>/dev/null &";  
//  $execMe = "rotctld -m $rotorID -r /dev/ttyUSB0 -T $tRotorIP -t $tRotorPort $rotorBaud > /dev/null 2>/dev/null &";
//echo "\n" . $execMe . "\n";
  if ($test == 1) {
    $execReport = "rotctl -m $rotorID -r $tRotorIP:$tRotorPort";
    $report =
      "To see any error details, click Disconnect Radio then start rotctl in Terminal with this line: <br><br><b>" .
      $execReport .
      "</b><br>";
    if ($test==1){
      echo $report;
    }
    doRotorLog(
      $test,
      "To see any error details, click Disconnect Radio, then start rotctl in Terminal with this line:" .
        PHP_EOL .
        "     " .
        $execReport
    );
  }
  shell_exec($execMe);
  usleep(100000);
  $rd = shell_exec("ps aux | grep '[r]otctld -'");
  $rd = str_replace("www", "<br>www", $rd);
  if (strlen($rd) > 0) {
    if ($test == 1) {
      $report = "<br>rotctld processes running:<br>" . $rd . "<br><br>";
      if ($test==1){
        echo $report;
      }
      doRotorLog($test, "rotctld processes running: " . PHP_EOL . "    " . $rd);
    }
  } else {
    $report = "\nError: Rotor control not started, not continuing.\n";
    doRotorLog($test, "Error: Rotor control not started, exiting startup.");
    if ($test==1){
      echo $report;
    }
 ////   exit();
  }

};

function doRotorLog($utest, $what)
{
  if ($utest == 1) {
    error_log(
      date("Y-m-d H:i:s", time()) . " " . $what . PHP_EOL,
      3,
      "/var/log/rigpi-rotor.log"
    );
  }
}

//echo "OK";
?>
