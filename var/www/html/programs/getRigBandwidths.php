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
  if ($tCap == "USB-D" || $tCap == "PKTUSB/USB-D") {
    $tCap = "PKTUSB";
  }
} else {
  $tCap = ""; // "";
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", 1); //$tMyRadio);
$row = $db->getOne("MySettings");
$tHamlibNr = $row["ID"];
$tComPort = "localhost:4532"; //$row["Port"];
if ($tCap == "") {
  $t = "rigctl -m 2 -r " . $tComPort . " m\n";
  $tCaps = shell_exec($t);
  $tCap = explode("\n", $tCaps);
  $tCaps = "Current=" . $tCap[1];
  //echo $t . "\n";
} else {
  $t =
    "rigctl -m 2 " .
    " -r " .
    $tComPort .
    " -u | grep -m 1 '" .
    $tCap .
    "\s\+\Normal'";
  //  echo $t;
  //" \get_mode_bandwidths $tCap | grep -m 1 'Normal'" .
  //    " VFOA \get_mode_bandwidths $tCap\n";
  //  echo $t . "\n";
  $tCaps = shell_exec($t);
  $tCaps = str_replace(": ", "=", $tCaps);
  $tCaps = str_replace(",", "", $tCaps);
}
//$tCaps = substr($tCaps, stripos($tCaps, ":") + 2);
echo $tCaps;
?>
