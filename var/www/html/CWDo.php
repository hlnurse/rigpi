<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require_once "/var/www/html/classes/CWPORTServer.class.php";

$tUser = $argv[1];
$tMyRadio = $argv[2];
$tMyKeyer = $argv[3];
$tMyCWPort = $argv[4];
$tPortTrans = $argv[5];
/*
$tUser = "admin"; //$argv[1];
$tMyRadio = "radio1"; //$argv[2];
$tMyKeyer = "rpk"; //$argv[3];
$tMyCWPort = "/dev/ttyS0"; // "/dev/ttyUSB1"; //$argv[4];
$tPortTrans = 0;
*/
$tMyRadio = substr($tMyRadio, strlen($tMyRadio) - 1);

if ($tMyKeyer != "non") {
  $port = 2 * $tMyRadio + 4530;
  $cwServer = new CWPortServer(
    "127.0.0.1",
    $port,
    $tMyCWPort,
    $tMyKeyer,
    $tMyRadio,
    $tUser,
    $tPortTrans
  );
  usleep(100000);
  $cwServer->infinite_loop();
}
?>
