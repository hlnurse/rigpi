<?php
//session_start;
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
require_once $dRoot . "/programs/disconnectRadioFunc.php";
sleep(1);
if (isset($_POST["radio"])) {
  $test = $_POST["test"]; //if 0, normal RSS mode, if 1 run from Terminal after confirm 'else' variables below
} else {
  $test = 1; //if 0, normal RSS mode, if 1 run from Terminal after confirm 'else' variables below
}
if ($test==1){
  echo "TEST MODE<p><p>";
}
$useVFOMode = 0;
$tRigPid = 0;
$report = "";
$reportOut = "";
if (isset($_POST["radio"])) {
  $tMyRadio = $_POST["radio"];
} else {
  $tMyRadio = '1';
}
if (isset($_POST["keyer"])) {
  $tMyKeyer = $_POST["keyer"];
} else {
  $tMyKeyer = "rpk1";
}
if (isset($_POST["user"])) {
  $tUsername = $_POST["user"];
} else {
  $tUsername = "admin"; // //only if user not specified
}
if (isset($_POST["port"])) {
  $tMyCWPort = $_POST["port"];
} else {
  $tMyCWPort = "/dev/ttyS0";
}
if (isset($_POST["rotorPort"])) {
  $tMyRotorPort = $_POST["rotorPort"];
} else {
  $tMyRotorPort = 4533;
}
if (isset($_POST["keyerPort"])) {
  $tMyKeyerPort = $_POST["keyerPort"];
} else {
  $tMyKeyerPort = 30040;
}
if (isset($_POST["keyerIP"])) {
  $tMyKeyerIP = $_POST["keyerIP"];
} else {
  $tMyKeyerIP = "127.0.0.43";
}
if (isset($_POST["keyerFunc"])) {
  $tMyKeyerFunc = $_POST["keyerFunc"];
} else {
  $tMyKeyerFunc = 0;
}
if (isset($_POST["tcpPort"])) {
  $tMyTCPPort = $_POST["tcpPort"];
} else {
  $tMyTCPPort = 30001;
}
if (isset($_POST["UDPPort"])) {
  $tMyUDPPort = $_POST["UDPPort"];
} else {
  $tMyUDPPort = 2333;
}

$setConf = "-C auto_power_on=1";
$tMyTCPPort = $tMyTCPPort;// + ($tMyRadio - 1); //allows for connection to any account
$report = "Radio: " . $tMyRadio . "<br>";
$report .= "User: " . $tUsername . "<br>";
$tClear = "cat /dev/null > /var/log/rigpi-radio.log";
exec($tClear);
$tClear = "cat /dev/null > /var/log/rigpi-rotor.log";
exec($tClear);
doLog($test, PHP_EOL . PHP_EOL . "RIGPI RADIO DIAGNOSTIC LOG" . PHP_EOL);
doRotorLog($test, PHP_EOL . PHP_EOL . "RIGPI ROTOR DIAGNOSTIC LOG" . PHP_EOL);
//Get data from MyRadio table
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$dRadio = $row["Radio"];
$report .= "Radio from settings: " . $dRadio . "<br>";
$dmodel = $row["Model"];
$port = $row["Port"];  //radio port picked up here
$tMyRotor = $row["Rotor"];
$rotorID = $row["RotorID"];
$rotorName = $row["RotorModel"];
$rotorBaud = $row["RotorBaud"];
$rotorStop = $row["RotorStop"];

if ($rotorBaud == "default") {
  $rotorBaud = "";
} else {
  $rotorBaud = "-s " . $rotorBaud;
}

$tMyRadioPort = $port;
if ($tMyRadioPort > 4530 && $tMyRadioPort < 5000) {
  $tMyCWRadio = $dRadio;//1 + ($tMyRadioPort - 4532) / 2;
} else {
  $tMyCWRadio = $dRadio;
}
if (!isset($_SESSION['myInstance'])){
  $_SESSION['myInstance']=1;
}
$instance=$_SESSION['myInstance'];

if ($test == 1) {
  echo $report;
  $u = "radio: " . $tMyRadio . " connection attempt" . PHP_EOL;
  $u .= "	radio port: " . $port . PHP_EOL;
  $u .= "	instance: " . $instance . PHP_EOL;
  $u .= "	keyer: " . $tMyKeyer . PHP_EOL;
  $u .= "	username: " . $tUsername . PHP_EOL;
  $u .= "	cw port: " . $tMyCWPort . PHP_EOL;
  $u .= "	keyer port (remote): " . $tMyKeyerPort . PHP_EOL;
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
  $u .= "	vfoMode: " . $useVFOMode;
  doLog($test, $u);
  $u = "rotor: " . $tMyRotor . " connection attempt" . PHP_EOL;
  $u .= "	rotor port: " . $tMyRotorPort . PHP_EOL;
  $u .= "	rotor ID: " . $rotorID . PHP_EOL;
  $u .= "	rotor Name: " . $rotorName . PHP_EOL;
  $u .= "	rotor Baud: " . $rotorBaud . PHP_EOL;
  $u .= "	rotor Stop: " . $rotorStop . PHP_EOL;
  doRotorLog($test, $u);
  echo "More information is in /var/log/rigpi-radio.log and /var/log/rigpi-rotor.log<br>";
}
$report .= "Port from settings: " . $port . "<br>";
$id = $row["ID"];
$ptt = $row["PTTMode"];
$tInvert = $row["KeyerInvert"];
$db->where("NUMBER", $id);
$row1 = $db->getOne("Radios");
if ($row1["SAMEAS"] > 0) {
  $report .=
    "ID from SAMEAS: " . $id . " is same as " . $row1["SAMEAS"] . "<br>";
  $id = $row1["SAMEAS"];
} else {
  $report .= "ID from settings: " . $id . "<br>";
}
$tBaud = $row["Baud"];
if ($tBaud == "default") {
  $baud = "";
} else {
  $baud = "-s $tBaud";
}
//echo "ID $id\n";
$service_port=0;
$service_host="";
/*if ($id == 2) {
  if (strpos($port,":")>1){
    $tPort=explode(":", $port);
    $service_host=$tPort[0];
    $service_port=$tPort[1];
  }else{
    $service_port = $port;
  }
} else {
  $service_port = $dRadio * 2 + 4530;
}
*/
  doLog($test, "Starting RigDo from exec.");
  $tRun="";
  $tw=1;
  $i=0;
  $pr = "";
  $i = 0;
//$vfos = shell_exec("rigctl -m 3073 -u | grep '[V]FO list -m 1'");
//if (strstr($vfos, "VFOA")) {
//  $vfoa = "VFOA";
//  $vfob = "VFOB";
//} else {
  $vfoa = "Main";
  $vfob = "Sub";

doLog($test, "Now starting RigPi radio control link.");
$vfoMode=0;
//$tRigExec = "php /var/www/html/RigDo.php $tUsername radio$dRadio $service_port $test $vfoa $vfoMode > /dev/null 2>/dev/null & echo $!";
$tRigExec = "php /var/www/html/RigDo.php $tUsername radio$dRadio $port $test $vfoa $vfoMode " . $_SESSION['myInstance'] . " > /dev/null 2>/dev/null & echo $!";
$report = "Now starting RigPi radio control link.<br><br> <b> $tRigExec </b><br><br>";
doLog($test, $tRigExec);
$pidRD = 0;
$pidRD = exec($tRigExec);
doLog($test, "tRigExec pid: " . $pidRD);
usleep(2000000);
$user = exec("ps aux | grep '[R]igDo'");
$report .=
  "RigDo Processes:<br><br>" .
  $user .
  "<br><br>RigDoPID: " .
  $pidRD .
  "; Radio: " .
  $dRadio .
  "; User: " .
  $tUsername .
  "; Keyer: " .
  $tMyKeyer .
  "; CW Port: " .
  $tMyCWPort .
  "<br><br>Now starting RigPi CW control link.<br>";
doLog(
  $test,
  "RigDo Processes:" .
    PHP_EOL .
    "    " .
    $user .
    PHP_EOL .
    "    RigDoPID: " .
    $pidRD .
    "; Radio: " .
    $dRadio .
    "; User: " .
    $tUsername .
    "; Keyer: " .
    $tMyKeyer .
    "; CW Port: " .
    $tMyCWPort .
    PHP_EOL .
    "    Now starting RigPi CW control link."
);
if ($test == 1) {
  echo $report;
}
//$tMyCWPort="/dev/ttyS0";
$tCW =
  $dRadio .
  "," .
  $tMyCWRadio .
  "," .
  $tUsername .
  "," .
  $tMyKeyer .
  "," .
  $tMyCWPort;
  $tMyCWRadio=$dRadio;
DoCW($dRadio, $tMyCWRadio, $tUsername, $tMyKeyer, $tMyCWPort, $test);
$report = "Now starting RigPi CW Server, radio is $dRadio and cw is $tMyCWRadio.<br>";
doLog(
  $test,
  "Now starting RigPi CW Server, radio is $dRadio and cw is $tMyCWRadio with command $tCW."
);
if ($test == 1) {
  echo $report;
}
$report = "Now starting RigPi TCP server on port $tMyTCPPort.<br>";
doLog($test, "Now starting RigPi TCP server on port $tMyTCPPort.");
DoTCP($dRadio, $tUsername, $tMyTCPPort);
if ($test == 1) {
  echo $report;
}
DoUDP($dRadio, $tUsername, $tMyUDPPort);
$sp = $service_port + 1;
$report = "Now starting RigPi UDP Server on port $tMyUDPPort.<br>";
doLog($test, "Now starting RigPi UDP Server on port $tMyUDPPort.");
if ($test == 1) {
  echo $report;
}
if ($tMyKeyerFunc==0){
//this is for rpk /dev/ttyS0
} elseif ($tMyKeyerFunc == 1) {
  //only start if this hamlib is at radio end for manual cw remote
  doLog($test, "Remote CW: $dRadio, $tUsername, $tMyKeyerPort, $tMyCWPort.");
  DoCWUDP($dRadio, $tUsername, $tMyKeyerPort, $tMyCWPort, $test);
  if ($test == 1) {
    $report = "Now starting RigPi remote CW link on UDP port $tMyCWPort.<br><br>";
    echo $report;
    doLog(
      $test,
      "Now starting RigPi remote CW link on UDP port $tMyCWPort."
    );
  }
}elseif ($tMyKeyerFunc == 2) {
  //only start if this hamlib is at remote end for manual cw remote
  DoCWUDP2($tMyKeyerIP, $tMyKeyerPort, $tInvert, $test);
  if ($test == 1) {
    echo "Now starting RigPi Remote end on port: $tMyKeyerPort, ip: $tMyKeyerIP, invert: $tInvert, test: $test " . PHP_EOL;
    doLog(
      $test,
          "Now starting RigPi Remote end on port: $tMyKeyerPort, IP: $tMyKeyerIP, invert: $tInvert, test: $test."
    );
  }
  }elseif ($tMyKeyerFunc == 3) {
    //only start if using CTS cw
      doLog($test, "Remote CW func 3: $dRadio, $tUsername, $tMyCWPort.");
  
      if ($test == 1) {
        echo "Now starting RigPi CTS driver on port: $tMyKeyerPort, CW POrt: $tMyCWPort, user: $tUsername, radio: $dRadio, test: $test.<br><br>";
        doLog(
          $test,
          "Now starting RigPi CTS 'ext' driver on port: $tMyKeyerPort, IP: $tMyKeyerIP, user: $tUsername, radio: $dRadio, test: $test."
        );
    }
    DoCWUDP($dRadio, $tUsername, $tMyKeyerPort, $tMyCWPort, $test);
    if ($test==1){
      echo "last error: " . error_get_last() . "\n";
    };
  
  }
//  $rotorID="1"; 
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
  $tRotorIP="";
  $tRotorPort=0;
  if (strpos($tMyRotorPort, ":")>0){
    $tR=explode(":", $tMyRotorPort);
    $tRotorPort=$tR[1];
    $tRotorIP=$tR[0];
  }else{
    $tRotorIP='0.0.0.0';
    $tRotorPort=$tMyRotorPort;
  }
  if ($rotorID == "1") {
    $r = "Hamlib Rotor Dummy";
    if ($tMyRotorPort == "None") {
      $report .=
        "Error: Set " .
        $rotorName .
        " Rotor port to 4531 + 2 * Radio number.\n";
      doRotorLog(
        $test,
        "Error: Set " . $rotorName . " Rotor port to 4531 + 2 * Radio number."
      );
      if ($test==1){
        echo $report;  //report bad error even if not in teast mode
      }
      exit();
    }
    $execDum =
    "rotctld -m 1 -T $tRotorIP -t $tRotorPort " .  //////////////////
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
        };
      doRotorLog($test, $report);
      exit();
    }
    $execDum =
//    "rotctl -m 2 -r 172.16.0.28:4533" .
    "rotctl -m 2 -r $tRotorIP:$tRotorPort " .
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
    echo $report;
    $rd = shell_exec("ps aux | grep '[r]otctld -'");
    $rd1 = $rd;
    $rd = str_replace("www", "<br>www", $rd);
    if (strlen($rd) == 0) {
      $rd = "NONE (rotctld failed to start)";
    }
    echo "<br>rotctld processes running: <br>" . $rd . "<br><br>";
    doRotorLog($test, "rotctld processes running: " . PHP_EOL . "    " . $rd1);
  }
} else {
  $execMe = "rotctld -m $rotorID -r $tMyRotorPort -T localhost -t $sp $rotorBaud > /dev/null 2>/dev/null &";
//  $execMe = "rotctld -m $rotorID -T $tRotorIP -t $tRotorPort $rotorBaud > /dev/null 2>/dev/null &";
//echo "\n" . $execMe . "\n";
  if ($test == 1) {
    $execReport = "rotctl -m $rotorID -r $tRotorIP:  $tRotorPort";
    $report =
      "To see any error details, click Disconnect Radio then start rotctl in Terminal with this line: <br><br><b>" .
      $execReport .
      "</b><br>";
    echo $report;
    doLog(
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
      echo $report;
      doRotorLog($test, "rotctld processes running: " . PHP_EOL . "    " . $rd);
    }
  } else {
    $report = "\nError: Rotor control not started, not continuing.\n";
    doRotorLog($test, "Error: Rotor control not started, exiting startup.");
    if ($test == 1) {
      echo $report;
    };
    exit();
  }
}
usleep(100000);
DoRotor($dRadio, $tUsername,  "$tRotorIP:$tRotorPort", $test);
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$report =
  "Report is complete. Select the text in this report, copy, then paste it to a destination (such as an email).";
doLog(
  $test,
  "hamlibDo.php is exiting. Reporting continues from RigServer.class.php."
);
doRotorLog(
  $test,
  "hamlibDo.php is exiting. Reporting continues from RotorServer.class.php."
);
if ($test == 1) {
  echo $report;
}else{
  $db->where("Radio", $dRadio);
  $row = $db->getOne("RadioInterface");
  $tFreq = $row["MainIn"];
  if ($row["MainIn"]=="OFF"){
    usleep(1000000);
    $row = $db->getOne("RadioInterface");
    $tFreq = $row["MainIn"];
  }
  echo $tFreq;   //this is important feedback to calling programs confirming connection
}

function DoCW($realRadio, $whichRadio, $Username, $myKeyer, $myCWPort, $myTest)
{

   $portTrans = 0;
  if ($realRadio != $whichRadio && $whichRadio > 0) {
    $portTrans = 1;
  }
  if ($whichRadio == 0) {
    $whichRadio = $realRadio;
  }
  //$myCWPort="localhost:30002";
  $tWKExec =
    "php /var/www/html/CWDo.php " .
    $Username .
    " radio" .
    $whichRadio .
    " " .
    $myKeyer .
    " " .
    $myCWPort .
    " " .
    $portTrans .
    " " .
    $myTest .
    " " .
    $_SESSION['myInstance'] .
    " > /dev/null 2> /dev/null & echo $!";
//S    $tWKExec="php /var/www/html/CWDo.php admin radio1 rpk1 /dev/ttyS0 0 1 > /dev/null 2> /dev/null & echo $!";

 doLog(
      $myTest,
      "Entering DoCW function:$tWKExec"
    );
  doLog($myTest, "Command to start CW server:" . PHP_EOL . "     " . $tWKExec);
     $pidWK = exec($tWKExec);
  doLog($myTest, "CW PID: " . $pidWK);
  require "/var/www/html/programs/vendor/autoload.php";
  system("gpio write 13 1"); //key up
  system("gpio mode 13 out");
}

function DoTCP($whichRadio, $Username, $port)
{
  $tTCPExec = "php /var/www/html/TCPDo.php $Username radio$whichRadio $port > /dev/null 2> /dev/null &";
  $pidTCP = exec($tTCPExec);
}

function DoUDP($whichRadio, $Username, $udpport)
{
  $tUDPExec = "php /var/www/html/UDPDo.php $Username radio$whichRadio $udpport > /dev/null 2> /dev/null &";
  $pidTCP = exec($tUDPExec);
}

function DoCWUDP($whichRadio, $Username, $tMyKeyerPort, $adapterPort, $utest)
{
  $tCWUDPExec = "php /var/www/html/CWUDPDo.php $Username radio$whichRadio $tMyKeyerPort $adapterPort $utest > /dev/null 2>/dev/null &";
  doLog(1, "Now starting RigPi CWUDP server: $tCWUDPExec");
  $pidCWUDP = exec($tCWUDPExec);
}

function DoCWUDP2($IP, $port, $invert, $utest)
{
//  $IP='172.16.0.5';
//  $port=30040;
//  $invert=0;
 $tCWUDPExec2="sudo /usr/share/rigpi/cw.sh " . $IP . " " . $port . " " . $invert; 
 //$tCWUDPExec2 = "php /var/www/html/programs/GPIO/GPIOInt1.php $IP $port $invert > /dev/null 2> /dev/null & ";
  doLog($utest, "Starting GPIOInt1 with: " . $tCWUDPExec2 );
   $pidCWUDP2 = exec($tCWUDPExec2);
   doLog(1, "UDP2: " . print_r(error_get_last()) . " out: " . $pidCWUDP2 . PHP_EOL);
}

function DoRotor($whichRotor, $Username, $port, $utest)
{
  $tRotorExec = "php /var/www/html/RotorDo.php $Username rotor$whichRotor $port $utest " . $_SESSION['myInstance'] . "> /dev/null 2> /dev/null & ";
  $pidRotor = shell_exec($tRotorExec);
  doRotorLog($utest, "DoRotor: " . $tRotorExec . " \n rotctl PID: " . $pidRotor);
}

function doLog($utest, $what)
{
  if ($utest == 1) {
    error_log(
      date("Y-m-d H:i:s", time()) . " " . $what . PHP_EOL,
      3,
      "/var/log/rigpi-radio.log"
    );
  }
}

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

?>
