<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine sets the settings data
 *
 * It must live in the programs folder
 */
$tField = $_POST["field"];
$tRadio = $_POST["radio"];
$tData = $_POST["data"];
$tTable = $_POST["table"];
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require "sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);

if ($tRadio != 0) {
  $db->where("Radio", $tRadio);
}

if ($tField == "MainOut") {
  $data = [
    "MainOut" => $tData,
    "MainOutCk" => "1",
    "MainIn" => $tData,
  ];
} elseif ($tField == "SubOut") {
  $data = [
    "SubOut" => $tData,
    "SubOutCk" => "1",
    "SubIn" => $tData,
  ];
} elseif ($tField == "ModeOut") {
  $data = [
    "ModeOut" => $tData,
    "ModeOutCk" => "1",
    "ModeIn" => $tData,
  ];
} elseif ($tField == "SplitOut") {
  $data = [
    "SplitOut" => $tData,
    "SplitOutCk" => "1",
    "SplitIn" => $tData,
  ];
} elseif ($tField == "PTTOut") {
  $data = [
    "PTTOut" => $tData,
    "PTTOutCk" => "1",
    "PTTIn" => $tData,
  ];
} elseif ($tField == "CommandOut") {
  $data = [
    "CommandOut" => $tData,
    "CommandOutCk" => "1",
  ];
} elseif ($tField == "RFGain") {
  $data = [
    "RFGain" => $tData,
    "RFGainCk" => "1",
  ];
} elseif ($tField == "AFGain") {
  $data = [
    "AFGain" => $tData,
    "AFGainCk" => "1",
  ];
} elseif ($tField == "MicLvl") {
  $data = [
    "MicLvl" => $tData,
    "MicLvlCk" => "1",
  ];
} elseif ($tField == "PwrOut") {
  $data = [
    "PwrOut" => $tData,
    "PwrOutCk" => "1",
  ];
} elseif ($tField == "Slave") {
  $data = [
    "Slave" => $tData,
    "SlaveCk" => "1",
  ];
} elseif ($tField == "WKSpeed") {
  $data = [
    $tField => $tData,
  ];
  $db->update($tTable, $data);
  $db->where("Radio", $tRadio);
  $tTable = "RadioInterface";
  $data = [
    "CWChangeCk" => 1,
  ];
  $db->where("Radio", $tRadio);
} else {
  $data = [
    $tField => $tData,
  ];
}
$db->update($tTable, $data);
echo "OK: " . $tData;

?>
