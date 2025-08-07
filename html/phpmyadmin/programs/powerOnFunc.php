<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
function doPwrOn($tMyRadio,$user){
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  $setConf="";
  if (isset($tMyRadio)) {
    $tMyRadio = $tMyRadio;
  } else {
    $tMyRadio = 1;
  }
  if (isset($user)) {
    $tUsername = $user;
  } else {
    $tUsername = "admin";  //only if user not specified
  }
  
  doGPIO();
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $db->where("Radio", $tMyRadio);
  $row = $db->getOne("MySettings");
  $id = $row["ID"];
  $db->where("NUMBER", $id);
  $row1 = $db->getOne("Radios");
  if ($row1["SAMEAS"] > 0) {
    $id = $row1["SAMEAS"];
  }
  $result = exec("rigctl -m $id -u | grep 'set Power Stat:'");
  if (strstr($result, "N")) {
    echo "<br>This radio does not support remote power on.<br><br>";
    exit();
  }
  $port = $row["Port"];
  $service_port = $tMyRadio * 2 + 4530; //$row['rigctldPort'];
  
  $tBaud = $row["Baud"];
  if ($tBaud == "default") {
    $baud = "";
  } else {
    $baud = "-s $tBaud";
  }
  
$stop = $row["Stop"];
  if (strlen($stop) > 1) {
    $stop = "";
  } else {
    if ($setConf==""){
      $setConf="-C stop_bits=".$stop;
    }else{
      $setConf .= ",stop_bits=".$stop;
    }
  }

  if (isset($_POST['instance'])){
    $instance=$_POST['instance'];
  }else{
    $instance=0;
  }
    if ($setConf==""){
      $setConf="-C instance=".$instance;
    }else{
      $setConf .= ",instance=".$instance;
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
    if ($setConf==""){
      $setConf="-C rts_state=$tRTS";
    }else{
      $setConf .= ",rts_state=$tRTS";
    }
    $tRTS = "-C rts_state=$tRTS";
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
    $tDTR = "-C dtr_state=$tDTR";
  }
  
  if (strlen($row["CIV_Code"]) == 7 || strlen($row["CIV_Code"]) == 0) {
    $tCIV = "";
  } else {
    $tCIV = "--civaddr " . $row["CIV_Code"];
    if (stripos($tCIV, "H") > 0) {
      $tCIV = str_replace("H", $tCIV, "");
      $tCIV = str_replace("h", $tCIV, "");
    }
  }
  
  $tComm ="--set-conf=auto_power_on=1";
  $tosend = $tComm . "\n";
  $testMe = "'[R]igDo.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep " . $testMe);
  
  if (isset($_SESSION['myInstance'])){
    $instance=$_SESSION['myInstance'];
  }
   $_SESSION['myInstance']=$instance;
    $execMe =
      "rigctld -m $id -r " .
      "$port $baud $tCIV $stop $tDTR $tRTS $tosend " .
      ">  /dev/null 2>/dev/null &";
    $execMe = str_replace("\n", "", $execMe);
    $execMe = $execMe . "\n";

  echo "\n" . $execMe . "\n";
  $res = shell_exec($execMe);
  $tosend = "f\n";
///  $gFreq = "rigctl  -m $id -r $port $baud $tCIV $stop $tDTR $tRTS $tosend ";
  //echo $gFreq . "\n";
//  usleep(10000000);
$freq=0;
$i=0;
while ($freq==0 && $i<20){
  $i=$i+1;
  $freq = shell_exec(
    "rigctl -m 2 -r localhost:$service_port $tosend");
  usleep(1000000);
  echo ("FREQUENCY: ". $freq."\n");
  
//  $freq="14225000\n";
}
  $freq = substr($freq, 0, strpos($freq, "\n"));
  if (ctype_digit(trim($freq))) {
    echo "<br>Power is on. (Frequency=$freq)<br><br>Use Connect Radio button on Tuner or Radio settings window to connect to radio.<br><br>";
  } else {
    echo "<br>$freq Power on failed. Try again.<br><br>";
  }
}

function doGPIO()
{
  system("gpio mode 2 out");
  system("gpio mode 3 out");
  system("gpio write 2 1");

  for ($i = 0; $i < 40; $i++) {
    system("gpio write 2 1");
    usleep(10);
    system("gpio write 2 0");
    usleep(10);
  }
  system("gpio write 3 1");
  usleep(100000);
  system("gpio write 3 0");
  system("gpio unexportall");
}

?>
