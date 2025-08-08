<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
if (isset($argv[1])) {
  $tUser = $argv[1];
} else {
  $tUser = "PTT";
}
if (isset($argv[2])) {
  $tMyRadio = $argv[2];
} else {
  $tMyRadio = "radio2";
}
if (isset($argv[3])) {
  $port = $argv[3];
} else {
  $port = 4532;
}
if (isset($argv[4])) {
  $test = $argv[4];
} else {
  $test = 0;
}
if (isset($argv[5])) {
  $vfo = $argv[5];
} else {
  $vfo = "VFOA";
}
if (isset($argv[6])) {
  $vfoMode = $argv[6];
} else {
  $vfoMode = 0;
}
$tMyRadio = substr($tMyRadio, strlen($tMyRadio) - 1);
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
require_once "/var/www/html/classes/RigServer_PTT.class.php";
$rigServer = new RigServer(
  "127.0.0.1",
  $port,
  $tMyRadio,
  $test,
  $vfo,
  $vfoMode
);
//$rigServer = new RigServer("127.0.0.1", 4532, $tMyRadio, $test, $vfo, $vfoMode);
usleep(100000);
$rigServer->infinite_loop();
?>
