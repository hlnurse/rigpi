<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine returns the specified radio data from rigctl
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
$tMyRadio = $_POST["myRadio"];
if (isset($_POST["mode"])) {
  $tCap = $_POST["mode"];
} else {
  $tCap = "";
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", 1);
$row = $db->getOne("MySettings");
$tHamlibNr = $row["ID"];
if ($tCap == "") {
  $t =
    "rigctl -m " .
    $tHamlibNr .
    //" \get_mode_bandwidths $tCap | grep -m 1 'Normal'" .
    " -r /dev/ttyUSB0 \m";
  //  echo $t;

  $tCaps = shell_exec($t);
  $tCap = explode("\n", $tCaps);
  $tCaps = "Current=" . $tCap[1];
  //echo $t . "\n";
} else {
  $t =
    "rigctl -m " .
    $tHamlibNr .
    //" \get_mode_bandwidths $tCap | grep -m 1 'Normal'" .
    " \get_mode_bandwidths $tCap  | grep -A 3 'Normal'";
  //echo $t . "\n";
  $tCaps = shell_exec($t);
}
//$tCaps = substr($tCaps, stripos($tCaps, ":") + 2);
echo $tCaps;
?>
