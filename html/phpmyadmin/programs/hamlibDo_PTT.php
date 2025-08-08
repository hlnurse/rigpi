<?php
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
require_once $dRoot . "/programs/disconnectRadioFunc.php";
if (isset($_POST["radio"])) {
  $test = $_POST["test"]; //if 0, normal RSS mode, if 1 run from Terminal after confirm 'else' variables below
} else {
  $test = 1; //if 0, normal RSS mode, if 1 run from Terminal after confirm 'else' variables below
}
$test = 1;
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
  $tMyRadio = 2;
}
if (isset($_POST["keyer"])) {
  $tMyKeyer = $_POST["keyer"];
} else {
  $tMyKeyer = "";
}
if (isset($_POST["user"])) {
  $tUsername = $_POST["user"];
} else {
  $tUsername = "PTT";
}
if (isset($_POST["port"])) {
  $tMyCWPort = $_POST["port"];
} else {
  $tMyCWPort = "";
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
  $tMyKeyerIP = "127.0.0.1";
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

$setConf = "--set-conf=auto_power_on=1";
$tMyUDPPort = 12060;
$tMyTCPPort = $tMyTCPPort + ($tMyRadio - 1); //allows for connection to any account
$report = "Radio: " . $tMyRadio . "<br>";
$report .= "User: " . $tUsername . "<br>";
$tClear = "cat /dev/null > /var/log/rigpi-radio.log";
exec($tClear);
doLog($test, PHP_EOL . PHP_EOL . "RIGPI RADIO DIAGNOSTIC LOG" . PHP_EOL);
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
$port = $row["Port"];
$tMyRadioPort = $port;
if ($tMyRadioPort > 4530 && $tMyRadioPort < 5000) {
  $tMyCWRadio = 1 + ($tMyRadioPort - 4532) / 2;
} else {
  $tMyCWRadio = $dRadio;
}
if ($test == 1) {
  echo $report;
  $u = "radio: " . $tMyRadio . " connection attempt" . PHP_EOL;
  $u .= "	radio port: " . $port . PHP_EOL;
  $u .= "	keyer: " . $tMyKeyer . PHP_EOL;
  $u .= "	username: " . $tUsername . PHP_EOL;
  $u .= "	cw port: " . $tMyCWPort . PHP_EOL;
  $u .= "	keyer port (remote): " . $tMyKeyerPort . PHP_EOL;
  $u .= "	keyer IP: " . $tMyKeyerIP . PHP_EOL;
  $u .= "	keyer: " . $tMyKeyer . PHP_EOL;
  $u .=
    "	keyer function: " .
    $tMyKeyerFunc .
    " (1=normal,2=radio,3=remote)" .
    PHP_EOL;
  $u .= "	tcp port: " . $tMyTCPPort . PHP_EOL;
  $u .= "	udp port: " . $tMyUDPPort . PHP_EOL;
  $u .= "	dRadio: " . $dRadio . PHP_EOL;
  $u .= "	tMyCWRadio: " . $tMyCWRadio . PHP_EOL;
  $u .= "	vfoMode: " . $useVFOMode;
  //		error_log("huh",3,"/var/log/rigpi-radio.log");
  doLog($test, $u);
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

if ($ptt == 2) {
  $ptt = "-P GPIO -p 17";
} else {
  $ptt = "";
}

$stop = $row["Stop"];
if (strlen($stop) > 1) {
  $stop = "";
} else {
  $stop = "--set-conf stop_bits=$stop";
}

$tRTS = $row["RTS"];
if (strlen($tRTS) > 4) {
  $tRTS = "";
} else {
  if ($tRTS == "high") {
    $tRTS = "ON";
  } else {
    $tRTS = "OFF";
  }
  $tRTS = "--set-conf rts_state=$tRTS";
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
  $tDTR = "--set-conf dtr_state=$tDTR";
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
$vfos = shell_exec("rigctl -m $id -u | grep '[V]FO list' -m 1");
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
// Get the IP address for the target host.
$db->where("Username", $tUsername);
$row = $db->getOne("Users");
$tRigDoPID_PTT = $row["rigDoPID"];
$report .= "RigDo_PTT.php rigDoPID from settings: " . $tRigDoPID . "<br>";
if ($test == 1) {
  echo $report;
}
if ($id == 2) {
  $service_port = $port;
} else {
  $service_port = $dRadio * 2 + 4530;
}
//$service_port = 4532;
$report = "service_port: " . $service_port . "<br>";
if ($test == 1) {
  echo $report;
}
$tR = "'RigDo_PTT.php $tUsername [r]adio$tMyRadio'";
$users = exec("ps aux | grep " . $tR);
$pos = stripos($users, "radio" . $tMyRadio);

disRadio($id, $tMyRadio, $tUsername, "");
//changes
if ($id == "1" || $id == "2") {
  $execDum = "rigctld -m $id -P RIG -t $service_port $ptt $vfoParam > /dev/null 2>/dev/null &";
  $r = "";
  if ($id == "1") {
    $r = "Hamlib Dummy";
  } else {
    $execDum = "sudo rigctl -m $id -r localhost:$service_port $vfoParam > /dev/null 2>/dev/null &";
    $r = "Hamlib Netctl";
    if (!$port > 4530) {
      $report .= "Set Radio port to 4530 + 2 * account number.\n";
      echo $report;
      exit();
    }
  }

  $report = "$r radio $id start attempt.<br>";
  exec($execDum);
  if ($test == 1) {
    $report .=
      "To see any error details, click Disconnect Radio, then start rigctl or rigctld in Terminal with this line: <br><br><b>" .
      $execDum .
      "</b><br>";
    echo $report;
    if ($id == 2) {
      $rd = shell_exec("ps aux | grep [r]igctl -m $id");
    } else {
      $rd = shell_exec("ps aux | grep [r]igctld -m $id ");
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
} else {
  doLog($test, "Starting rigctld from exec.");
  $execMe = "rigctld -m $id -r $port -t $service_port $ptt $baud $tCIV $stop $tDTR $tRTS $vfoParam $setConf > /dev/null 2>/dev/null &";
  $execMe = str_replace("    ", " ", $execMe);
  doLog($test, "exec: $execMe");
  if ($test == 1) {
    $tT = str_replace("rigctld", "rigctl", $execMe);
    $tT1 = explode(">", $tT);
    $report =
      "To see any error details, click Disconnect Radio then start rigctl in Terminal with this line: <br><br><b>" .
      str_replace("-t $service_port ", "", $tT1[0]) .
      "</b><br>";
    echo $report;
  }
  exec($execMe);
  usleep(1000000);
  $pr = "";
  $i = 0;
  while (strstr($pr, "rigctld") == false) {
    $pr = shell_exec("ps aux | grep [r]igctld -m $id");
    doLog($test, "Startup attempt: $i $pr");
    //    echo "\npr: " . $pr . "\n";
    usleep(500000);
    $i += 1;
    //    echo "i: " . $i . "\n";
    if ($i == 4) {
      $report = "\nError: Rig control not started.\n";
      exec($execMe);
      doLog($test, "Restarted $execMe");
      while (strstr($pr, "rigctld") == false) {
        $pr = shell_exec("ps aux | grep [r]igctld -m $id");
        doLog($test, "Startup (2) attempt: $i $pr");
        exec($execMe);
        usleep(500000);
        $i += 1;
        if ($i == 10) {
          $pr = exec($execMe . " " . "-vvvvv");
          doLog($test, $pr);
          echo $report;
          doLog($test, "Exiting startup attempt: $i $pr");
          exit();
        }
      }
    }
  }
  $gF = "rigctl -m 2 -r 127.0.0.1:$service_port f\n";
  $rd = shell_exec($gF);
  $i = 0;
  //  $rd = 1;
  $pr = "";
  while (intval($rd) < 1) {
    $rd = shell_exec($gF);
    doLog($test, "f attempt: $i-- $gF $rd");
    $i += 1;
    if ($i == 30) {
      $report = "\nError: Rig control for frequency not started.\n";
      echo $report;
      exit();
    }
    usleep(1000000);
  }
  if ($test == 1) {
    $pr = shell_exec("ps aux | grep [r]igctl");
    $pr = str_replace("root", "<br>root", $pr);
    $report = "<br>rigctl(d) processes running:<br>" . $pr . "<br><br>";
    echo $report;
    doLog($test, "Rigctl(d) processes running:\n" . $pr . "\n");
  }
}
doLog($test, "Now starting RigPi radio control link.");
$tRigExec = "php /var/www/html/RigDo_PTT.php $tUsername radio$dRadio $service_port $test $vfoa $vfoMode > /dev/null 2>/dev/null & echo $!";
$report = "Now starting RigPi radio control link. $tRigExec <br>";
doLog($test, $tRigExec);
$pidRD = 0;
$pidRD = exec($tRigExec);
doLog($test, "tRigExec pid: " . $pidRD);
usleep(2000000);
$user = exec("ps aux | grep '[R]igDo'");
$db->where("Radio", $tMyRadio);
$row = $db->getOne("RadioInterface");
$curFreq = $row["MainIn"];
doLog($test, "Current frequency: " . $curFreq);
if (intval($curFreq) < 1000) {
  $i = 0;
  while (strlen($curFreq) < 6 && $i < 20) {
    usleep(1000000);
    $row = $db->getOne("RadioInterface");
    $curFreq = $row["MainIn"];
    $i += 1;
    doLog($test, "Current frequency attempt $i: " . $curFreq);
  }
}
if ($test == 1) {
  if (strlen($curFreq) > 5 && intval($curFreq) > 1000) {
    $report .= "Congratulations, $dmodel is connected!<br><br>";
    $report .=
      "<b>Main frequency is " .
      (number_format($curFreq, 0, "", ".") . " MHz</b><br><br>");
    doLog(
      $test,
      "Radio $dmodel is connected, main frequency is " .
        number_format($curFreq, 0, "", ".") .
        " MHz"
    );
  } else {
    echo "The Radio control startup process failed.<br><br>";
    doLog($test, "The Radio control startup process failed.");
  }
} else {
  if (strlen($curFreq) > 5) {
    echo "Congratulations, $dmodel is connected!<p><p>";
    echo "Main frequency is <h2c>" .
      (number_format($curFreq, 0, "", ".") . " MHz</h2c><br>");
  } else {
    echo "<h3c>The Radio control startup process failed. " .
      "<br><br>Make sure the radio is on and connected. <br><br>Learn more by using Test Radio in Advanced Radio Settings.</h3c>";
  }
}

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
/*
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
if ($tMyKeyerFunc == 1) {
  //only start if this hamlib is at radio end for cw remote
  doLog($test, "Remote CW: $dRadio, $tUsername, $tMyKeyerPort.");
  DoCWUDP($dRadio, $tUsername, $tMyKeyerPort);
  if ($test == 1) {
    $report = "Now starting RigPi remote CW link on UDP port $tMyKeyerPort.<br><br>";
    echo $report;
    doLog(
      $test,
      "Now starting RigPi remote CW link on UDP port $tMyKeyerPort."
    );
  }
}
if ($tMyKeyerFunc == 2) {
  //only start if this hamlib is at remote end for cw remote
  DoCWUDP2($tMyKeyerIP, $tMyKeyerPort, $tInvert);
  if ($test == 1) {
    echo "Now starting RigPi remote CW interrupt driver on port: $tMyKeyerPort, IP: $tMyKeyerIP.<br><br>";
    doLog(
      $test,
      "Now starting RigPi remote CW interrupt driver on port: $tMyKeyerPort, IP: $tMyKeyerIP."
    );
  }
}
*/
/*if ($rotorID == "1" || $rotorID == "2") {
  $tRotorPort = $tMyRotorPort;
  $report .= "Rotor: " . $rotorID . " (" . $rotorName . ")<br>";
  $report .= "Rotor port: " . $tMyRotorPort . "<br>";
  doRotorLog(
    $test,
    "Now starting RigPi Rotor Server on port: $tRotorPort using rotor " .
      $rotorID .
      "."
  );
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
      echo $report;
      exit();
    }
    $execDum =
      "rotctld -m 1 -T 127.0.0.1 -t " .
      $tRotorPort .
      " > /dev/null 2>/dev/null &";
  } else {
    $r = "Hamlib Rotor Netctl rotctl";
    if (!$tMyRotorPort > 4532 || $tRotorPort == "None") {
      $report .=
        "Error: Set " .
        $rotorName .
        " Rotor port to 4531 + 2 * other Radio number.\n";
      echo $report;
      doRotorLog($test, $report);
      exit();
    }
  }
  $report .= "Rotor ID $rotorID (" . $rotorName . ") start attempt.<br>";
  doRotorLog($test, "Rotor ID $rotorID (" . $rotorName . ") start attempt.");
  shell_exec($execDum);
  if ($test == 1) {
    $execReport = "rotctl -m 1 -T 127.0.0.1";
    $report .=
      "To see any error details, click Disconnect Radio, then start rotctl in Terminal with this line: <br><br><b>" .
      "rotctl -m 1" .
      "</b><br>";
    echo $report;
    $rd = shell_exec("ps aux | grep [r]otctld -");
    $rd1 = $rd;
    $rd = str_replace("www", "<br>www", $rd);
    if (strlen($rd) == 0) {
      $rd = "NONE (rotctld failed to start)";
    }
    echo "<br>rotctld processes running: <br>" . $rd . "<br><br>";
    doRotorLog($test, "rotctld processes running: " . PHP_EOL . "    " . $rd1);
  }
} else {
  $execMe = "rotctld -m $rotorID -r $tMyRotorPort -T 127.0.0.1 -t $sp $rotorBaud > /dev/null 2>/dev/null &";
  if ($test == 1) {
    $execReport = "rotctl -m $rotorID -r $tMyRotorPort";
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
  usleep(1000000);
  $rd = shell_exec("ps aux | grep [r]otctld -");
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
    echo $report;
    exit();
  }
}
usleep(1000000);
DoRotor($dRadio, $tUsername, $tMyRotorPort, $test);
//	DoRotor($dradio,$tUsername,'4533');
*/
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
  "hamlibDo_PTT.php is exiting. Reporting continues from RigServer_PTT.class.php."
);
/*doRotorLog(
  $test,
  "hamlibDo.php is exiting. Reporting continues from RotorServer.class.php."
);
*/ if (
  $test == 1
) {
  echo $report;
}
//}
/*
function DoCW($realRadio, $whichRadio, $Username, $myKeyer, $myCWPort, $myTest)
{
  doLog(
    $myTest,
    "Entering DoCW function: $realRadio, $whichRadio, $Username, $myKeyer, $myCWPort."
  );

  $portTrans = 0;
  if ($realRadio != $whichRadio && $whichRadio > 0) {
    $portTrans = 1;
  }
  if ($whichRadio == 0) {
    $whichRadio = $realRadio;
  }
  $tWKExec =
    "php /var/www/html/CWDo.php " .
    $Username .
    " radio" .
    $realRadio .
    " " .
    $myKeyer .
    " " .
    $myCWPort .
    " " .
    $portTrans .
    " > /dev/null 2>/dev/null &";
  doLog($myTest, "Command to start CW server:" . PHP_EOL . "     " . $tWKExec);

  $pidWK = exec($tWKExec);
  doLog($myTest, "CW PID: " . $pidWK);
  require "/var/www/html/programs/vendor/autoload.php";
  system("gpio write 13 1"); //key up
  system("gpio mode 13 out");
}

function DoTCP($whichRadio, $Username, $port)
{
  $tTCPExec = "php /var/www/html/TCPDo.php $Username radio$whichRadio $port> /dev/null 2>/dev/null &";
  $pidTCP = exec($tTCPExec);
}

function DoUDP($whichRadio, $Username, $udpport)
{
  $tUDPExec = "php /var/www/html/UDPDo.php $Username radio$whichRadio $udpport> /dev/null 2>/dev/null &";
  $pidTCP = exec($tUDPExec);
}

function DoCWUDP($whichRadio, $Username, $udpport)
{
  //note, IP is 127.0.0.1 for case where this rigpi is controlling radio...hardwired in CWUDPDo.php
  $tCWUDPExec = "php /var/www/html/CWUDPDo.php $Username radio$whichRadio $udpport > /dev/null 2>/dev/null &";
  doLog(1, "Now starting RigPi CW UDP server: $tCWUDPExec");
  $pidCWUDP = exec($tCWUDPExec);
}

function DoCWUDP2($IP, $port, $invert)
{
  $tCWUDPExec2 =
    "sudo /usr/share/rigpi/cw.sh " . $IP . " " . $port . " " . $invert;
  $pidCWUDP2 = exec($tCWUDPExec2);
}

function DoRotor($whichRotor, $Username, $port, $utest)
{
  ///need to add parameters!!!!
  $tRotorExec = "php /var/www/html/RotorDo.php $Username rotor$whichRotor $port $utest > /dev/null 2>/dev/null &";
  $pidRotor = exec($tRotorExec);
  doRotorLog($utest, "DoRotor: " . $tRotorExec . " PID: " . $pidRotor);
}
*/
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
/*
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
*/
?>
