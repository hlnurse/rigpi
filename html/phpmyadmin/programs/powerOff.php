<?php

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
require_once $dRoot . "/programs/disconnectRadioFunc.php";
if (isset($_POST["radio"])) {
  $tMyRadio = $_POST["radio"];
} else {
  $tMyRadio = 1;
}
if (isset($_POST["user"])) {
  $tUsername = $_POST["user"];
} else {
  $tUsername = "admin";  //only if user not specified
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$id = $row["ID"];
$result = exec("rigctl -m $id -u | grep 'set Power Stat:'");
if (strstr($result, "N")) {
  echo "<br>This radio does not support remote power off. Radio control will be disconnected.<br><br>";
  exit();
}
$db->where("NUMBER", $id);
$row1 = $db->getOne("Radios");
if ($row1["SAMEAS"] > 0) {
  //	echo("ID from SAMEAS: ".$id." is same as ".$row1["SAMEAS"]."<br>");
  $id = $row1["SAMEAS"];
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
  $stop = "--set-conf stop_bits=2";
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

if (strlen($row["CIV_Code"]) == 7 || strlen($row["CIV_Code"]) == 0) {
  $tCIV = "";
} else {
  $tCIV = "--civaddr " . $row["CIV_Code"];
  if (stripos($tCIV, "H") > 0) {
    $tCIV = str_replace("H", $tCIV, "");
    $tCIV = str_replace("h", $tCIV, "");
  }
}

//$tComm = sprintf("0x87")." 0";
$tComm = "\set_powerstat 0";
$tosend = $tComm . "\n";
$testMe = "'[R]igDo.php $tUsername [r]adio$tMyRadio'";
$users = exec("ps aux | grep " . $testMe);

//if (strlen($users)>0){
//	$execMe="rigctl -m 2 -t $service_port $tosend";
//}else{
$execMe = "rigctl -m $id -r $port $baud $tCIV $stop $tDTR $tRTS $tosend > /dev/null &";
//	echo $execMe;
//}
//	echo $users;
shell_exec($execMe);
disRadio($id, $tMyRadio, $tUsername, "");
echo "Power is off.";

?>
