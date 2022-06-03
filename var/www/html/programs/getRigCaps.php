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
//if (!empty($_POST["myRadio"])) {
//$tMyRadio = $_GET["myRadio"];
$tMyRadio = $_POST["myRadio"];
//} else {
///  $tMyRadio = 0;
//}
if (!empty($_POST["cap"])) {
  $tCap = $_POST["cap"];
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

//$tHamlibNr = "3073";
//$tHamlibNr = 3073;
//$tCap = "Mode list: ";
$t = "rigctl -m " . $tHamlibNr . " -u | grep -m 1 " . "'$tCap'";
//echo $t . "\n";
$tCaps = shell_exec($t);
$tCaps = substr($tCaps, stripos($tCaps, ":") + 2);
//$tCaps="Mode:".$tCaps
echo $tCaps;
?>
