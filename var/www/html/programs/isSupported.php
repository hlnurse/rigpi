<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
require_once "./sqldata.php";
require_once "../classes/MysqliDb.php";
$tInfo = $_POST["getSet"];
$tMyRadio = $_POST["radio"];
$tText = $_POST["text"];
$dRoot = "/var/www/html";
$service_port = 4532 + ($tMyRadio - 1) * 2;
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$resultID = $row["ID"];
//if ($resultID==1){
//	echo 0;
//}
if ($tMyRadio == 2 || $tMyRadio == 1) {
  echo 1;
} else {
  if ($tInfo == "get") {
    $result = shell_exec("rigctl -m $resultID -u | grep 'Get level:'");
  } elseif ($tInfo == "set") {
    $result = shell_exec("rigctl -m $resultID -u | grep 'Set level:'");
  } else {
    $result = shell_exec("rigctl -m $resultID -u | grep Serial");
  }
  if ($result == "") {
    $result = "No settings found for this radio.";
  }
  if (strstr($result, $tText)) {
    echo 1;
  } else {
    echo 0;
  }
}

?>
