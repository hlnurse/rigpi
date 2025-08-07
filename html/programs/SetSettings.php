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
//$row=$db->getOne($tTable);
//$db->setLockMethod("READ")->lock("RadioInterface");
if ($tField == "MainOut") {
    $data = ["MainIn" => $tData, "MainOutCk" => 1, "MainOut" => $tData];
    // $db->where("MainOutCk",0);
} elseif ($tField == "SubOut") {
    $data = ["SubIn" => $tData, "SubOut" => $tData, "SubOutCk" => 1];
} elseif ($tField == "ModeOut") {
    $data = ["ModeOut" => $tData, "ModeOutCk" => "1"];
} elseif ($tField == "Test") {
    $db->where("Radio", $tRadio);
    $data = ["Test" => $tData];
} elseif ($tField == "BWOut") {
    $data = ["BWOut" => $tData, "BWOutCk" => "1"];
} elseif ($tField == "SplitOut") {
    $data = ["SplitOut" => $tData, "SplitOutCk" => "1"];
} elseif ($tField == "PTTOut") {
    $data = ["PTTIn" => $tData, "PTTOut" => $tData, "PTTOutCk" => "1"];
} elseif ($tField == "CommandOut") {
    $data = ["CommandOut" => $tData, "CommandOutCk" => "1"];
} elseif ($tField == "RFGain") {
    $data = ["RFGain" => $tData, "RFGainCk" => "1"];
} elseif ($tField == "AFGain") {
    $data = ["AFGain" => $tData, "AFGainCk" => "1"];
    $db->where("Radio", $tRadio);
} elseif ($tField == "USBAFGain") {
    $data = ["USBAFGain" => $tData, "USBAFGainCk" => "1"];
    $db->where("Radio", $tRadio);
} elseif ($tField == "MicLvl") {
    $data = ["MicLvl" => $tData, "MicLvlCk" => "1"];
} elseif ($tField == "PwrOut") {
    $data = ["PwrOut" => $tData, "PwrOutCk" => "1"];
} elseif ($tField == "Slave") {
    $data = ["Slave" => $tData, "SlaveCk" => "1"];
} elseif ($tField == "CWIn") {
    $data = ["CWIn" => $tData];
} elseif ($tField == "CWOut") {
    $data = ["CWOut" => $tData];
} elseif ($tField == "WKRemoteIP") {
    $db->where("Radio", $tRadio);
    $tTable = "Keyer";
    $data = ["WKRemoteIP" => $tData];
    $db->update($tTable, $data);
    $tTable = "RadioInterface";
    $data = [
        "CWChangeCk" => 1,
    ];
} elseif ($tField == "WKRemotePort") {
    $db->where("Radio", $tRadio);
    $tTable = "Keyer";
    $data = ["WKRemotePort" => $tData];
    $db->update($tTable, $data);
    $tTable = "RadioInterface";
    $data = [
        "CWChangeCk" => 1,
    ];
} elseif ($tField == "WKSpeed") {
    $db->where("Radio", $tRadio);
    $tTable = "Keyer";
    $data = ["WKSpeed" => $tData];
    $db->update($tTable, $data);
    $tTable = "RadioInterface";
    $data = [
        "CWChangeCk" => 1,
    ];
} elseif ($tField == "CWInWK") {
    $db->where("Radio", $tRadio);
    $db->update($tTable, $tData);
    $tTable = "RadioInterface";
    $data = [
        "CWInWKCk" => 1,
    ];
} elseif ($tField == "waitReset") {
    $db->where("Radio", $tRadio);
    $data = [$tField => $tData];
} else {
    $data = [$tField => $tData];
}
echo "error?: " . error_get_last();
$db->where("Radio", $tRadio);
echo "\nradio: " . $tRadio . "\n";
$id = $db->update($tTable, $data);
//$db->unlock();
//$d=$db->getOne($tTable);
//echo "here " . $d['AFGain']. " " . json_encode($data) . " " . $tTable . " " . "last error ".$db->getLastError();
//if ($id){
//echo "\n".$id . "\nOK: " . print_r($data) . "\n";
//not supported  $db->query("UNLOCK TABLES;");
?>
