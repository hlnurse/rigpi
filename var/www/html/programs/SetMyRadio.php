<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine sets the specified radio data for the assigned account
 *
 * It must live in the programs folder
 */
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
require_once "./sqldata.php";
require_once "../classes/MysqliDb.php";
$curRadio = $_POST["i"]; //radio number for this acccount
$manufacturer = $_POST["m"]; //Manufacturer
$model = $_POST["o"]; //radio model
$port = $_POST["p"]; //tty port
$keyerPort = $_POST["kp"]; //tty port
$baud = $_POST["u"]; //baud rate
$bits = $_POST["b"]; //stop bits (
$parity = $_POST["a"]; //parity
$stop = $_POST["s"]; //stop
$id = $_POST["d"]; //hamlib id
$civ = $_POST["c"]; //ci-v code for Icom
$rName = $_POST["n"]; //radio name
$keyer = $_POST["k"];
$rts = $_POST["r"];
$dtr = $_POST["t"];
$spl = $_POST["sp"];
$xmd = $_POST["xd"];
$pttMode = $_POST["pt"];
$pttCmd = $_POST["pM"];
$pttCAT = $_POST["pA"];
$pttDelay = $_POST["dl"];
$slavePort = $_POST["slp"];
$slaveBaud = $_POST["slb"];
$slaveCommand = $_POST["slc"];
$showVideo = $_POST["sv"];
$pttLatch = $_POST["pl"];
if ($rName == "Dummy") {
  $port = "None";
}
if ($keyer == "None" || $keyer == "via CAT") {
  $keyerPort = "None";
}
$data1 = [
  "Selected" => 0,
];
$data = [
  "Selected" => 1,
  "Manufacturer" => $manufacturer,
  "Model" => $model,
  "Port" => $port,
  "KeyerPort" => $keyerPort,
  "Baud" => $baud,
  "Bits" => $bits,
  "Parity" => $parity,
  "Stop" => $stop,
  "CIV_Code" => $civ,
  "ID" => $id,
  "DTR" => $dtr,
  "RTS" => $rts,
  "RadioName" => $rName,
  "Keyer" => $keyer,
  "DisableSplitPolling" => $spl,
  "PTTMode" => $pttMode,
  "PTTCmd" => $pttCmd,
  "PTTCAT" => $pttCAT,
  "PTTDelay" => $pttDelay,
  "SlavePort" => $slavePort,
  "SlaveBaud" => $slaveBaud,
  "SlaveCommand" => $slaveCommand,
  "TransmitLevel" => $xmd,
  "ShowVideo" => $showVideo,
  "PTTLatch" => $pttLatch,
];
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->update("MySettings", $data1);
if ($port > 4500 && $port < 5000) {
} else {
  usleep(100000);
  $db->where("Port", $port);
  $db->where("Port", "", "!=");
  $db->where("Radio", $curRadio, "!=");
  $db->where("Radio", 0, "!=");
  $row = $db->getOne("MySettings");
  if ($db->count > 0) {
    if (strtolower($row["Port"]) != "none") {
      echo $row["Port"] .
        " is already in use in the account for radio " .
        $row["RadioName"] .
        ".";
      exit();
    }
  }
  $db->where("SlaveCommand", "None", "!=");
  $db->where("Radio", 0, "!=");
  $db->where("Radio", $curRadio, "!=");
  $row = $db->getOne("MySettings");
  if ($db->count > 0) {
    if (strtolower($slaveCommand) != "none") {
      echo "A slave is already used in the account for radio " .
        $row["RadioName"] .
        ".";
      exit();
    }
  }
  $db->where("Port", $slavePort);
  $db->where("SlavePort", "", "!=");
  $db->where("Radio", 0, "!=");
  $row = $db->getOne("MySettings");
  if ($db->count > 0) {
    if (strtolower($row["SlavePort"]) != "none") {
      echo $row["SlavePort"] .
        " is already in use as a slave port in the account for radio " .
        $row["RadioName"] .
        ".";
      exit();
    }
  }
}
$db->where("KeyerPort", $keyerPort);
$db->where("Radio", $curRadio, "!=");
$db->where("KeyerPort", "None", "!=");
$db->where("KeyerPort", "", "!=");
$db->where("Radio", "0", "!=");
$db->where("Keyer", $keyer);
$row = $db->getOne("MySettings");
if ($db->count > 0) {
  echo "CW Port " .
    $port .
    " is already in use in the account for radio " .
    $row["RadioName"] .
    ".";
  exit();
}
$db->where("Radio", $curRadio);
if ($db->update("MySettings", $data)) {
  echo "Settings for radio " .
    $curRadio .
    " (" .
    $manufacturer .
    " " .
    $model .
    ") were saved.";
} else {
  echo "update failed: " . $db->getLastError();
}

?>
