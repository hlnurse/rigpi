<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
if (isset($POST['getSet'])){
 $tInfo = $_POST["getSet"];
}else{
  $tInfo='set';
}
if (isset($POST['radio'])){
 $tMyRadio = $_POST["radio"];
}else{
  $tMyRadio=1;
}
if (isset($POST['text'])){
 $tText = $_POST["text"];
}else{
  $tText="PREAMP";
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$resultID = $row["ID"];
$resultRemoteID = $row["remoteID"];
//$service_port = 4532 + ($tMyRadio - 1) * 2;

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
