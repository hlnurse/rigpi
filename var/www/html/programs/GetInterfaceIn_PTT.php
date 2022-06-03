<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine gets the settings data
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/GetBand.php";
require_once $dRoot . "/programs/GetUserFieldFunc.php";
require_once $dRoot . "/programs/GetFldigiLogFunc.php";
$tUser = $_POST["un"];
$tRadio = $_POST["radio"];
if ($tRadio == "") {
  $tRadio = "1";
}
$myCall = "W6HN"; //$_POST["myCall"];
require $dRoot . "/programs/sqldata.php";
$sQuery = "SELECT MainIn,SplitIn,SubIn,ModeIn,SMeterIn,CWBusy,PTTIn,RadioData,Transmit,Slave,RFGain,AFGain,PwrOut,MicLvl,CommandOut from RadioInterface where Radio='$tRadio'";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$result = $con->query($sQuery);
if ($row = $result->fetch_assoc()) {
  $tBand = GetBandFromFrequency($row["MainIn"]);
  $data =
    $row["MainIn"] .
    "`" .
    $row["SplitIn"] .
    "`" .
    $row["SubIn"] .
    "`" .
    $row["ModeIn"] .
    "`" .
    $row["SMeterIn"] .
    "`" .
    $tBand .
    "`" .
    $row["CWBusy"] .
    "`" .
    $row["PTTIn"] .
    "`" .
    $row["RadioData"] .
    "`" .
    $row["Transmit"] .
    "`" .
    $row["Slave"] .
    "`" .
    $row["RFGain"] .
    "`" .
    $row["AFGain"] .
    "`" .
    $row["PwrOut"] .
    "`" .
    $row["MicLvl"] .
    "`" .
    $row["CommandOut"] .
    "`";
} else {
  $data = "NG";
}
$sQuery = "UPDATE RadioInterface SET Close_Watch=CURRENT_TIMESTAMP() WHERE Radio='$tRadio'";
$result = $con->query($sQuery);

echo $data;
$isFl = getUserField($tUser, "LogFldigi");
if ($isFl == 1) {
  getFlLog($tUser, $myCall, $tRadio);
}

?>
