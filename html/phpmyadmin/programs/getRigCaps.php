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
if (isset($_POST['myRadioName'])){
  $tMyRadioName = $_POST["myRadioName"];  //this is model, might be at station end.
}else{
  $tMyRadioName = "Dummy";
}
if (!empty($_POST["myRadio"])) {
  $tMyRadio = $_POST["myRadio"];
} else {
  $tMyRadio = 1;
}
if (!empty($_POST["cap"])) {
  $tCap = $_POST["cap"];
} else {
  $tCap = "Mode list";
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("MODEL", $tMyRadioName);
$row = $db->getOne("Radios");
if ($row){
  $tHamlibNr = $row["NUMBER"];
//  $tPort=$row["Port"]-1;
}else{
  $tHamlibNr = 1;
  $tPort=4532;
};
$t = "rigctl -m " . $tHamlibNr . " -u | grep " . "'$tCap'";
$tCaps = shell_exec($t);
$command = "echo " . escapeshellarg($tCaps) . " | tail -n2";
$secondLine = shell_exec($command);
//echo $secondLine; // Output: SECOND LINE

//$tCaps=shell_exec("sed -n '2p' $tCaps");
//echo $tCaps . "\n";

$tCaps = trim(substr($secondLine, stripos($tCaps, ":") + 3));
$tCaps = str_replace("PKTLSB", "LSB-D", $tCaps);
$tCaps = str_replace("PKTUSB", "USB-D", $tCaps);
//$tCaps="Mode:".$tCaps;
echo $tCaps;
?>
