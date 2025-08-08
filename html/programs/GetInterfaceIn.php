<?php
/*
 * RigPi Tuner
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
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
$myCall = $_POST["myCall"];
if ($tRadio == "") {
    $tRadio = "1";
    $tUser = "admin";
    $myCall = "W6HN";
}

require $dRoot . "/programs/sqldata.php";
$sQuery = "SELECT * from RadioInterface where Radio='$tRadio'";
$con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
//  $con->query("LOCK TABLES RadioInterface WRITE;");
$result = $con->query($sQuery);
if ($row = $result->fetch_assoc()) {
    $tBand = GetBandFromFrequency($row["MainIn"]);
    $tMode = $row["ModeIn"];
    //  if (strlen($tMode)==0){
    //    $tMode=$row["ModeIn"];
    //  }
    $data =
        $row["MainIn"] .
        "`" .
        $row["SplitIn"] .
        "`" .
        $row["SubIn"] .
        "`" .
        $tMode .
        "`" .
        $row["SMeterIn"] . //there is no out
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
        $row["USBAFGain"] .
        "`" .
        $row["CommandOut"] .
        "`" .
        $row["BWIn"] .
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
//  $con->query("UNLOCK TABLES;");
?>
